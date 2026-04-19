<?php
session_start();
require_once "../model/user.php";
require_once "../model/teacher.php";
require_once "../model/dataAccess.php";

$id = trim($_GET['id'] ?? $_GET['teacher_email'] ?? '');
$isEditing = isset($_GET['edit']) && $_GET['edit'] === '1';

$session_email = trim($_SESSION['email_address'] ?? '');
$viewerUserType = strtolower(trim($_SESSION['user_type'] ?? ''));
$isOwnProfile = ($session_email !== '' && $viewerUserType === 'teacher' && strtolower($session_email) === strtolower($id));

$teacher = null;
$hourly_rate = null;
$subjects = [];
$allSubjects = [];
$reviews = [];
$canReview = false;
$eligibleReviewStudents = [];
$selectedReviewStudentEmail = trim($_GET['review_student'] ?? '');
$existingReview = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_teacher_profile' && $isOwnProfile) {
        $bio = trim($_POST['bio'] ?? '');
        $experience = trim($_POST['experience'] ?? '');
        $teacher_type = trim($_POST['teacher_type'] ?? '');
        $hourly_rate = trim($_POST['hourly_rate'] ?? '');
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $contact_number = trim($_POST['contact_number'] ?? '');

        try {
            updateTeacherProfile($id, $bio, $experience, $teacher_type, $hourly_rate);
            updateUserProfile($id, $first_name, $last_name, $contact_number);
            $_SESSION['profile_success'] = "Profile updated successfully.";
        } catch (Exception $e) {
            $_SESSION['profile_error'] = "Failed to update profile.";
        }

        header("Location: teacherProfile.php?id=" . urlencode($id));
        exit();
    }

    if ($action === 'add_subject' && $isOwnProfile) {
        $subject_id = $_POST['subject_id'] ?? '';

        if ($subject_id) {
            try {
                addTeacherSubject($id, $subject_id);
                $_SESSION['profile_success'] = "Subject added successfully.";
            } catch (Exception $e) {
                $_SESSION['profile_error'] = "Failed to add subject. It may already be assigned.";
            }
        }

        header("Location: teacherProfile.php?id=" . urlencode($id) . "&edit=1");
        exit();
    }

    if ($action === 'delete_subject' && $isOwnProfile) {
        $subject_id = $_POST['subject_id'] ?? '';

        if (empty($subject_id)) {
            $_SESSION['profile_error'] = "Error: Subject ID is missing.";
            header("Location: teacherProfile.php?id=" . urlencode($id) . "&edit=1");
            exit();
        }

        try {
            $result = removeTeacherSubject($id, $subject_id);

            if ($result) {
                $_SESSION['profile_success'] = "Subject removed successfully.";
            } else {
                $_SESSION['profile_error'] = "Subject could not be removed.";
            }
        } catch (Exception $e) {
            $_SESSION['profile_error'] = "Database error: " . $e->getMessage();
        }

        header("Location: teacherProfile.php?id=" . urlencode($id) . "&edit=1");
        exit();
    }

    if ($action === 'submit_review' && !$isOwnProfile && in_array($viewerUserType, ['student', 'parent'], true)) {
        $rating = (int) ($_POST['rating'] ?? 0);
        $review_text = trim($_POST['review_text'] ?? '');
        $reviewStudentEmail = '';

        if ($rating < 1 || $rating > 10) {
            $_SESSION['profile_error'] = "Please choose a rating between 1 and 10.";
            header("Location: teacherProfile.php?id=" . urlencode($id));
            exit();
        }

        if ($review_text === '') {
            $_SESSION['profile_error'] = "Please write a short review.";
            header("Location: teacherProfile.php?id=" . urlencode($id));
            exit();
        }

        if ($viewerUserType === 'student') {
            if (!hasStudentBookedTeacher($session_email, $id)) {
                $_SESSION['profile_error'] = "You can only review teachers you have booked.";
                header("Location: teacherProfile.php?id=" . urlencode($id));
                exit();
            }

            $reviewStudentEmail = $session_email;
        } else {
            $eligibleReviewStudents = getEligibleStudentsForParentReview($session_email, $id);
            $eligibleEmails = array_map(function ($student) {
                return strtolower(trim($student->email_address ?? ''));
            }, $eligibleReviewStudents);

            $reviewStudentEmail = trim($_POST['student_email'] ?? '');

            if ($reviewStudentEmail === '' || !in_array(strtolower($reviewStudentEmail), $eligibleEmails, true)) {
                $_SESSION['profile_error'] = "Please select one of your linked students who has booked this teacher.";
                header("Location: teacherProfile.php?id=" . urlencode($id));
                exit();
            }
        }

        try {
            saveTeacherReview($id, $session_email, $viewerUserType, $reviewStudentEmail, $rating, $review_text);
            $_SESSION['profile_success'] = "Your review has been saved.";
        } catch (Exception $e) {
            $_SESSION['profile_error'] = "Unable to save your review right now.";
        }

        $redirectUrl = "teacherProfile.php?id=" . urlencode($id);
        if ($viewerUserType === 'parent' && $reviewStudentEmail !== '') {
            $redirectUrl .= "&review_student=" . urlencode($reviewStudentEmail);
        }

        header("Location: " . $redirectUrl);
        exit();
    }
}

if ($id) {
    $teacher = getTeacherDetails($id);
    $hourly_rate = getTeacherRate($id);
    $subjects = getTeacherSubjectsWithYearGroups($id);
    $reviews = getTeacherReviews($id);

    if ($isEditing && $isOwnProfile) {
        $allSubjects = getAllSubjects();
    }

    if (!$isOwnProfile && $session_email !== '' && in_array($viewerUserType, ['student', 'parent'], true)) {
        if ($viewerUserType === 'student') {
            if (hasStudentBookedTeacher($session_email, $id)) {
                $canReview = true;
                $selectedReviewStudentEmail = $session_email;
                $existingReview = getTeacherReviewByReviewer($id, $session_email, $selectedReviewStudentEmail);
            }
        } else {
            $eligibleReviewStudents = getEligibleStudentsForParentReview($session_email, $id);

            if (!empty($eligibleReviewStudents)) {
                $canReview = true;

                $eligibleEmails = array_map(function ($student) {
                    return strtolower(trim($student->email_address ?? ''));
                }, $eligibleReviewStudents);

                if ($selectedReviewStudentEmail === '' || !in_array(strtolower($selectedReviewStudentEmail), $eligibleEmails, true)) {
                    $selectedReviewStudentEmail = $eligibleReviewStudents[0]->email_address;
                }

                $existingReview = getTeacherReviewByReviewer($id, $session_email, $selectedReviewStudentEmail);
            }
        }
    }
}

if (!$teacher) {
    die("Teacher not found.");
}

$success_message = $_SESSION['profile_success'] ?? '';
$error_message = $_SESSION['profile_error'] ?? '';
unset($_SESSION['profile_success'], $_SESSION['profile_error']);

require_once "../view/teacherProfileView.php";
?>
