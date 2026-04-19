<?php
session_start();
require_once "../model/user.php";
require_once "../model/dataAccess.php";

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$session_email = trim($_SESSION['email_address']);

$userResults = getUserByEmail($session_email);
if (empty($userResults)) {
    die("User not found.");
}
$user = $userResults[0];

$userType = strtolower(trim($_SESSION['user_type'] ?? ($user->user_type ?? '')));
if (!isset($_SESSION['user_type']) && !empty($user->user_type)) {
    $_SESSION['user_type'] = $user->user_type;
}

$userAddress = getUserAddress($session_email);

$parentTypeOptions = ['Father', 'Mother', 'Guardian', 'Other'];
$studentTypeOptions = ['Preschool', 'Primary School', 'Secondary School', 'Colleges, Sixth Form'];

function getParentTypeByEmail($email_address) {
    global $pdo;
    try {
        $stmt = $pdo->prepare(
            "SELECT parent_type
             FROM parents
             WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?))
             LIMIT 1"
        );
        $stmt->execute([trim($email_address)]);
        $parentType = $stmt->fetchColumn();
        return $parentType !== false ? (string) $parentType : '';
    } catch (Exception $e) {
        return '';
    }
}

function updateParentTypeByEmail($email_address, $parent_type) {
    global $pdo;
    $stmt = $pdo->prepare(
        "UPDATE parents
         SET parent_type = ?
         WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?))"
    );
    return $stmt->execute([$parent_type, trim($email_address)]);
}

function updateStudentTypeByEmail($email_address, $student_type) {
    global $pdo;
    $stmt = $pdo->prepare(
        "UPDATE students
         SET student_type = ?
         WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?))"
    );
    return $stmt->execute([$student_type, trim($email_address)]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'update_profile':
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $contact_number = trim($_POST['contact_number'] ?? '');
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            $parent_type = array_key_exists('parent_type', $_POST)
                ? trim((string) $_POST['parent_type'])
                : getParentTypeByEmail($session_email);
            $gender = trim($_POST['gender'] ?? '');
            $ethnicity = trim($_POST['ethnicity'] ?? '');
            $date_of_birth = ($userType === 'student') ? trim($_POST['date_of_birth'] ?? '') : null;
            $student_type = ($userType === 'student') ? trim($_POST['student_type'] ?? '') : '';

            if ($first_name === '' || $last_name === '') {
                $_SESSION['profile_error'] = "First and last name are required.";
                header("Location: myprofile.php#profile");
                exit();
            }

            if ($userType === 'parent' && $parent_type !== '' && !in_array($parent_type, $parentTypeOptions, true)) {
                $_SESSION['profile_error'] = "Please select a valid relationship to the student.";
                header("Location: myprofile.php#profile");
                exit();
            }

            if ($userType === 'student' && $student_type !== '' && !in_array($student_type, $studentTypeOptions, true)) {
                $_SESSION['profile_error'] = "Please select a valid student type.";
                header("Location: myprofile.php#profile");
                exit();
            }

            // FIX: Use !empty() so teachers (null) and empty strings pass through
            if ($userType === 'student' && !empty($date_of_birth) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_of_birth)) {
                $_SESSION['profile_error'] = "Invalid date of birth format.";
                header("Location: myprofile.php#profile");
                exit();
            }

            if ($new_password !== '') {
                if ($new_password !== $confirm_password) {
                    $_SESSION['profile_error'] = "Passwords do not match.";
                    header("Location: myprofile.php#password");
                    exit();
                }
                updateUserProfile($session_email, $first_name, $last_name, $contact_number, $new_password, $date_of_birth ?: null, $gender, $ethnicity);
            } else {
                updateUserProfile($session_email, $first_name, $last_name, $contact_number, null, $date_of_birth ?: null, $gender, $ethnicity);
            }

            if ($userType === 'parent') {
                updateParentTypeByEmail($session_email, $parent_type !== '' ? $parent_type : null);
            }

            if ($userType === 'student') {
                updateStudentTypeByEmail($session_email, $student_type !== '' ? $student_type : null);
            }

            // Handle address update
            $post_code = trim($_POST['post_code'] ?? '');
            $address_line1 = trim($_POST['address_line1'] ?? '');
            $addressline2 = trim($_POST['addressline2'] ?? '');
            $town = trim($_POST['town'] ?? '');
            $county = trim($_POST['county'] ?? '');

            if ($post_code !== '' && $address_line1 !== '') {
                if ($userType === 'parent') {
                    try {
                        $pdo->beginTransaction();

                        $stmt = $pdo->prepare("SELECT * FROM address WHERE post_code = ? AND address_line1 = ?");
                        $stmt->execute([$post_code, $address_line1]);
                        if (!$stmt->fetch()) {
                            $stmt = $pdo->prepare("INSERT INTO address (post_code, address_line1, addressline2, town, county) VALUES (?, ?, ?, ?, ?)");
                            $stmt->execute([$post_code, $address_line1, $addressline2 ?: null, $town ?: null, $county ?: null]);
                        } else {
                            $stmt = $pdo->prepare("UPDATE address SET addressline2 = ?, town = ?, county = ? WHERE post_code = ? AND address_line1 = ?");
                            $stmt->execute([$addressline2, $town, $county, $post_code, $address_line1]);
                        }

                        // Re-link parent
                        $stmt = $pdo->prepare("DELETE FROM user_address WHERE email_address = ?");
                        $stmt->execute([$session_email]);
                        $stmt = $pdo->prepare("INSERT INTO user_address (email_address, address_line1, post_code) VALUES (?, ?, ?)");
                        $stmt->execute([$session_email, $address_line1, $post_code]);

                        // Re-link all students to the same address
                        $linkedStudents = getAllStudentsForParent($session_email);
                        foreach ($linkedStudents as $student) {
                            $studentEmail = trim($student->email_address ?? '');
                            if ($studentEmail) {
                                $stmt = $pdo->prepare("DELETE FROM user_address WHERE email_address = ?");
                                $stmt->execute([$studentEmail]);
                                $stmt = $pdo->prepare("INSERT INTO user_address (email_address, address_line1, post_code) VALUES (?, ?, ?)");
                                $stmt->execute([$studentEmail, $address_line1, $post_code]);
                            }
                        }

                        $pdo->commit();
                    } catch (Exception $e) {
                        if ($pdo->inTransaction()) {
                            $pdo->rollBack();
                        }
                        error_log("Shared address update failed: " . $e->getMessage());
                    }
                } else {
                    updateUserAddress($session_email, $post_code, $address_line1, $addressline2, $town, $county);
                }
            }

            $_SESSION['profile_success'] = "Profile updated successfully.";
            header("Location: myprofile.php#profile");
            exit();

        case 'update_student_profile':
            if ($userType !== 'parent') {
                $_SESSION['profile_error'] = "Unauthorized action.";
                header("Location: myprofile.php");
                exit();
            }

            $student_email = trim($_POST['student_email'] ?? '');
            $first_name = trim($_POST['first_name'] ?? '');
            $last_name = trim($_POST['last_name'] ?? '');
            $contact_number = trim($_POST['contact_number'] ?? '');
            $student_type = trim($_POST['student_type'] ?? '');
            $date_of_birth = trim($_POST['date_of_birth'] ?? '');
            $gender = trim($_POST['gender'] ?? '');
            $ethnicity = trim($_POST['ethnicity'] ?? '');

            $linkedStudents = getAllStudentsForParent($session_email);
            $ownedEmails = array_map(
                function ($student) {
                    return strtolower(trim((string) ($student->email_address ?? '')));
                },
                $linkedStudents
            );

            if ($student_email === '' || !in_array(strtolower($student_email), $ownedEmails, true)) {
                $_SESSION['profile_error'] = "You are not authorized to update this student.";
                header("Location: myprofile.php");
                exit();
            }

            if ($first_name === '' || $last_name === '') {
                $_SESSION['profile_error'] = "Student first and last name are required.";
            } elseif ($student_type !== '' && !in_array($student_type, $studentTypeOptions, true)) {
                $_SESSION['profile_error'] = "Please select a valid student type.";
            } elseif ($date_of_birth !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_of_birth)) {
                $_SESSION['profile_error'] = "Invalid date of birth format.";
            } else {
                updateUserProfile($student_email, $first_name, $last_name, $contact_number, null, $date_of_birth ?: null, $gender, $ethnicity);
                updateStudentTypeByEmail($student_email, $student_type !== '' ? $student_type : null);
                $_SESSION['profile_success'] = "Student profile updated successfully.";
            }

            header("Location: myprofile.php#students");
            exit();

        case 'update_rate':
            if ($userType === 'teacher') {
                $hourly_rate = $_POST['hourly_rate'] ?? '';
                if (is_numeric($hourly_rate) && $hourly_rate >= 0) {
                    updateTeacherRate($session_email, $hourly_rate);
                    $_SESSION['profile_success'] = "Hourly rate updated successfully.";
                } else {
                    $_SESSION['profile_error'] = "Invalid rate provided.";
                }
                header("Location: myprofile.php#rate");
            }
            exit();

        case 'add_slot':
            if ($userType === 'teacher') {
                $slot_date = $_POST['slot_date'] ?? '';
                $start_time = $_POST['start_time'] ?? '';
                $end_time = $_POST['end_time'] ?? '';

                if ($slot_date && $start_time && $end_time) {
                    if ($start_time >= $end_time) {
                        $_SESSION['profile_error'] = "End time must be after start time.";
                    } else {
                        if (addLessonSlot($session_email, $slot_date, $start_time, $end_time)) {
                            $_SESSION['profile_success'] = "Lesson slot added successfully.";
                        } else {
                            $_SESSION['profile_error'] = "Failed to add lesson slot.";
                        }
                    }
                } else {
                    $_SESSION['profile_error'] = "All fields are required.";
                }
                header("Location: myprofile.php#slots");
            }
            exit();

        case 'cancel_booking':
            $slot_id = $_POST['slot_id'] ?? '';
            if ($slot_id && $userType === 'teacher') {
                if (releaseTeacherSlot($slot_id, $session_email)) {
                    $_SESSION['profile_success'] = "Booking cancelled successfully. The slot is now available again.";
                } else {
                    $_SESSION['profile_error'] = "Unable to cancel booking. It may already be cancelled or you do not have permission.";
                }
            }
            header("Location: myprofile.php#slots");
            exit();

        case 'delete_slot':
            $slot_id = $_POST['slot_id'] ?? '';
            if ($slot_id && $userType === 'teacher') {
                if (deleteTeacherSlot($slot_id, $session_email)) {
                    $_SESSION['profile_success'] = "Slot deleted successfully.";
                } else {
                    $_SESSION['profile_error'] = "Unable to delete slot. It may be booked or you do not have permission.";
                }
            }
            header("Location: myprofile.php#slots");
            exit();

              case 'delete_account':
            $confirm_password = $_POST['confirm_password'] ?? '';
            $confirm_text = $_POST['confirm_text'] ?? '';

            if ($confirm_text !== 'DELETE') {
                $_SESSION['profile_error'] = "Please type DELETE to confirm account deletion.";
                header("Location: myprofile.php#delete");
                exit();
            }

            $userCheck = loginUser($session_email, $confirm_password);
            if (empty($userCheck)) {
                $_SESSION['profile_error'] = "Incorrect password. Account deletion cancelled.";
                header("Location: myprofile.php#delete");
                exit();
            }

            try {
                if (deleteUserByEmail($session_email)) {
                    session_destroy();
                    header("Location: home.php?deleted=1");
                    exit();
                } else {
                    $_SESSION['profile_error'] = "Unable to delete account. Please try again.";
                    header("Location: myprofile.php#delete");
                    exit();
                }
            } catch (Exception $e) {
                $_SESSION['profile_error'] = "Error deleting account: " . $e->getMessage();
                header("Location: myprofile.php#delete");
                exit();
            }

        default:
            header("Location: myprofile.php");
            exit();
    }
}

$teacher = null;
$bookings = [];
$linkedStudents = [];
$studentBookingsMap = [];
$parentType = '';
$studentType = '';
$userTypeLabel = $userType !== '' ? ucfirst($userType) : 'User';

if ($userType === 'teacher') {
    $teacher = getTeacherDetails($session_email);
    $bookings = getTeacherBookingsWithInvoices($session_email);
} elseif ($userType === 'parent') {
    $parentType = getParentTypeByEmail($session_email);
    $linkedStudents = getAllStudentsForParent($session_email);

    foreach ($linkedStudents as $student) {
        $studentEmail = trim((string) ($student->email_address ?? ''));
        $studentKey = strtolower($studentEmail);
        $studentBookings = $studentEmail !== '' ? getCurrentBookingsForUser($studentEmail) : [];

        foreach ($studentBookings as $booking) {
            $booking->student_first_name = $student->first_name ?? '';
            $booking->student_last_name = $student->last_name ?? '';
        }

        $studentBookingsMap[$studentKey] = $studentBookings;
        $bookings = array_merge($bookings, $studentBookings);
    }

    usort(
        $bookings,
        function ($a, $b) {
            return strcmp(($a->slot_date . $a->start_time), ($b->slot_date . $b->start_time));
        }
    );
} elseif ($userType === 'student') {
    $bookings = getCurrentBookingsForUser($session_email);
    $studentType = getStudentTypeByEmail($session_email);
}

$success_message = $_SESSION['profile_success'] ?? '';
$error_message = $_SESSION['profile_error'] ?? '';
unset($_SESSION['profile_success'], $_SESSION['profile_error']);

require_once "../view/myProfileView.php";