<?php
require_once '../model/dataAccess.php';
require_once '../model/user.php';
require_once '../model/student.php';
require_once '../model/parent.php';

$error_message = '';
$step = 1;
$student_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $posted_step = (int)($_POST['step'] ?? 1);

    if ($posted_step === 1) {
        // Validate step 1 fields only
        $student_first_name = trim($_POST['student_first_name'] ?? '');
        $student_last_name  = trim($_POST['student_last_name'] ?? '');
        $student_email      = trim($_POST['student_email_address'] ?? '');
        $student_password   = $_POST['student_password'] ?? '';
        $student_answer     = trim($_POST['student_security_answer'] ?? '');

        if (empty($student_first_name) || empty($student_last_name) ||
            empty($student_email) || empty($student_password) || empty($student_answer)) {
            $error_message = "All required fields must be filled.";
            $step = 1;
        } elseif (!filter_var($student_email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Please enter a valid student email address.";
            $step = 1;
        } else {
            // Pass to step 2
            $step = 2;
            $student_data = $_POST;
        }

    } elseif ($posted_step === 2) {
        // Collect all data and create accounts
        $student_first_name     = trim($_POST['student_first_name'] ?? '');
        $student_last_name      = trim($_POST['student_last_name'] ?? '');
        $student_contact        = trim($_POST['student_contact_number'] ?? '');
        $student_email          = trim($_POST['student_email_address'] ?? '');
        $student_password       = $_POST['student_password'] ?? '';
        $student_security_q     = $_POST['student_security_question'] ?? '';
        $student_security_a     = trim($_POST['student_security_answer'] ?? '');

        $parent_first_name  = trim($_POST['first_name'] ?? '');
        $parent_last_name   = trim($_POST['last_name'] ?? '');
        $parent_contact     = trim($_POST['contact_number'] ?? '');
        $parent_email       = trim($_POST['email_address'] ?? '');
        $parent_password    = $_POST['password'] ?? '';
        $security_question  = $_POST['security_question'] ?? '';
        $security_answer    = trim($_POST['security_answer'] ?? '');

        if (empty($parent_first_name) || empty($parent_last_name) ||
            empty($parent_email) || empty($parent_password) || empty($security_answer)) {
            $error_message = "All required fields must be filled.";
            $step = 2;
            $student_data = $_POST;
        } elseif (!filter_var($parent_email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Please enter a valid parent email address.";
            $step = 2;
            $student_data = $_POST;
        } elseif (strcasecmp($student_email, $parent_email) === 0) {
            $error_message = "Student and parent must have different email addresses.";
            $step = 2;
            $student_data = $_POST;
        } else {
            $existing_student = getUserByEmail($student_email);
            $existing_parent  = getUserByEmail($parent_email);

            if (!empty($existing_student)) {
                $error_message = "Student email is already registered.";
                $step = 2;
                $student_data = $_POST;
            } elseif (!empty($existing_parent)) {
                $error_message = "Parent email is already registered.";
                $step = 2;
                $student_data = $_POST;
            } else {
                try {
                    $pdo->beginTransaction();

                    $parent_created = addUser($parent_first_name, $parent_last_name, $parent_contact,
                        $parent_email, 'parent', $parent_password, $security_question, $security_answer);

                    $student_created = addUser($student_first_name, $student_last_name, $student_contact,
                        $student_email, 'student', $student_password, $student_security_q, $student_security_a);

                    if ($parent_created && $student_created) {
                        addParent($parent_email, null);
                        addStudent($student_email, null);
                        linkStudentParent($student_email, $parent_email);
                        $pdo->commit();
                        header("Location: sign_in.php");
                        exit();
                    } else {
                        $pdo->rollBack();
                        $error_message = "Failed to create accounts. Please try again.";
                        $step = 2;
                        $student_data = $_POST;
                    }
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $error_message = "Database error. Please try again later.";
                    $step = 2;
                    $student_data = $_POST;
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error_message = "An unexpected error occurred. Please try again.";
                    $step = 2;
                    $student_data = $_POST;
                }
            }
        }
    }
}

include '../view/signupView.php';
?>