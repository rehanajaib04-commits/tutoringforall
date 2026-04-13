<?php
session_start();
require_once "../model/user.php";
require_once "../model/dataAccess.php";

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$session_email = $_SESSION['email_address'];
$userType      = $_SESSION['user_type'] ?? '';

// ─────────────────────────────────────────────────────────
// HANDLE ALL POST ACTIONS
// ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_profile':
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $contact_number = $_POST['contact_number'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';
            
            if (!empty($new_password)) {
                if ($new_password !== $confirm_password) {
                    $_SESSION['profile_error'] = "Passwords do not match.";
                    header("Location: myprofile.php#password");
                    exit();
                }
                updateUserProfile($session_email, $first_name, $last_name, $contact_number, $new_password);
            } else {
                updateUserProfile($session_email, $first_name, $last_name, $contact_number);
            }
            $_SESSION['profile_success'] = "Profile updated successfully.";
            header("Location: myprofile.php#profile");
            exit();
            
        case 'update_student_profile':
            // Parent updating their child's profile
            if ($userType !== 'parent') {
                $_SESSION['profile_error'] = "Unauthorized action.";
                header("Location: myprofile.php");
                exit();
            }
            
            $student_email = $_POST['student_email'] ?? '';
            $first_name = $_POST['first_name'] ?? '';
            $last_name = $_POST['last_name'] ?? '';
            $contact_number = $_POST['contact_number'] ?? '';
            
            // Verify this student belongs to the logged-in parent
            $linkedStudents = getAllStudentsForParent($session_email);
            $ownedEmails = array_map(function($s) { 
                return strtolower(trim($s->email_address)); 
            }, $linkedStudents);
            
            if (!in_array(strtolower(trim($student_email)), $ownedEmails)) {
                $_SESSION['profile_error'] = "You are not authorized to update this student.";
                header("Location: myprofile.php");
                exit();
            }
            
            if (empty($first_name) || empty($last_name)) {
                $_SESSION['profile_error'] = "First and last name are required.";
            } else {
                updateUserProfile($student_email, $first_name, $last_name, $contact_number);
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
                    $_SESSION['profile_error'] = "Unable to cancel booking. It may already be cancelled or you don't have permission.";
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
                    $_SESSION['profile_error'] = "Unable to delete slot. It may be booked or you don't have permission.";
                }
            }
            header("Location: myprofile.php#slots");
            exit();
            
        case 'delete_account':
            $confirm_password = $_POST['confirm_password'] ?? '';
            $confirm_text = $_POST['confirm_text'] ?? '';
            
            // Verify confirmation text
            if ($confirm_text !== 'DELETE') {
                $_SESSION['profile_error'] = "Please type DELETE to confirm account deletion.";
                header("Location: myprofile.php#delete");
                exit();
            }
            
            // Verify password
            $userCheck = loginUser($session_email, $confirm_password);
            if (empty($userCheck)) {
                $_SESSION['profile_error'] = "Incorrect password. Account deletion cancelled.";
                header("Location: myprofile.php#delete");
                exit();
            }
            
            // Delete the user (cascading should handle related records if DB is set up with FK constraints)
            // If not, we might need to manually clean up, but let's assume FK constraints handle it
            try {
                // Delete from specific type tables first to avoid FK constraint issues if no cascade
                if ($userType === 'teacher') {
                    $stmt = $pdo->prepare("DELETE FROM teachers WHERE email_address = ?");
                    $stmt->execute([$session_email]);
                } elseif ($userType === 'student') {
                    $stmt = $pdo->prepare("DELETE FROM student_parent WHERE student_email_address = ?");
                    $stmt->execute([$session_email]);
                    $stmt = $pdo->prepare("DELETE FROM students WHERE email_address = ?");
                    $stmt->execute([$session_email]);
                } elseif ($userType === 'parent') {
                    // Get linked students before deleting parent
                    $linkedStudents = getAllStudentsForParent($session_email);
                    // Delete student_parent links
                    $stmt = $pdo->prepare("DELETE FROM student_parent WHERE parent_email_address = ?");
                    $stmt->execute([$session_email]);
                    // Delete from parents table
                    $stmt = $pdo->prepare("DELETE FROM parents WHERE email_address = ?");
                    $stmt->execute([$session_email]);
                    // Note: Students remain as independent accounts
                }
                
                // Delete from users table (main account)
                $stmt = $pdo->prepare("DELETE FROM users WHERE email_address = ?");
                $stmt->execute([$session_email]);
                
                // Destroy session and redirect
                session_destroy();
                header("Location: home.php?deleted=1");
                exit();
                
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

// ─────────────────────────────────────────────────────────
// LOAD DATA FOR VIEW
// ─────────────────────────────────────────────────────────
$userResults = getUserByEmail($session_email);
if (empty($userResults)) {
    die("User not found.");
}
$user = $userResults[0];

$teacher       = null;
$bookings      = [];
$linkedStudents = []; // For parents with multiple students

if ($userType === 'teacher') {
    $teacher = getTeacherDetails($session_email);
} elseif ($userType === 'parent') {
    // Load all students linked to this parent
    $linkedStudents = getAllStudentsForParent($session_email);
    
    // Load bookings for all linked students
    foreach ($linkedStudents as $student) {
        $studentBookings = getCurrentBookingsForUser($student->email_address);
        foreach ($studentBookings as $booking) {
            $booking->student_first_name = $student->first_name;
            $booking->student_last_name = $student->last_name;
        }
        $bookings = array_merge($bookings, $studentBookings);
    }
    
    // Sort by date
    usort($bookings, function($a, $b) {
        return strcmp($a->slot_date . $a->start_time, $b->slot_date . $b->start_time);
    });
} elseif ($userType === 'student') {
    $bookings = getCurrentBookingsForUser($session_email);
}

// Flash messages from redirects
$success_message = $_SESSION['profile_success'] ?? '';
$error_message   = $_SESSION['profile_error']   ?? '';
unset($_SESSION['profile_success'], $_SESSION['profile_error']);

require_once "../view/myProfileView.php";
?>