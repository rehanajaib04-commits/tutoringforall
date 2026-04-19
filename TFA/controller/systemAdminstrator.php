<?php
session_start();

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=systemAdminstrator.php");
    exit();
}

if (strtolower(trim($_SESSION['user_type'] ?? '')) !== 'admin') {
    header("Location: myprofile.php");
    exit();
}

require_once "../model/user.php";
require_once "../model/dataAccess.php";

$message = "";
$message_type = "info";
$formData = [
    'first_name' => '',
    'last_name' => '',
    'contact_number' => '',
    'email_address' => '',
    'user_type' => 'student',
    'security_question' => '',
    'security_answer' => '',
    'date_of_birth' => '',
    'gender' => '',
    'ethnicity' => '',
];

if (isset($_GET['status']) && $_GET['status'] === 'success') {
    $message = 'User added successfully.';
    $message_type = 'success';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['addUser'])) {
    $formData = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'contact_number' => trim($_POST['contact_number'] ?? ''),
        'email_address' => trim($_POST['email_address'] ?? ''),
        'user_type' => trim($_POST['user_type'] ?? 'student'),
        'security_question' => trim($_POST['security_question'] ?? ''),
        'security_answer' => trim($_POST['security_answer'] ?? ''),
        'date_of_birth' => trim($_POST['date_of_birth'] ?? ''),
        'gender' => trim($_POST['gender'] ?? ''),
        'ethnicity' => trim($_POST['ethnicity'] ?? ''),
    ];
    $password = $_POST['password'] ?? '';

    if ($formData['first_name'] === '' || $formData['last_name'] === '' || $formData['email_address'] === '' || $password === '') {
        $message = 'Please complete all required fields.';
        $message_type = 'danger';
    } elseif (!filter_var($formData['email_address'], FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $message_type = 'danger';
    } elseif (!in_array($formData['user_type'], ['student', 'teacher', 'parent', 'admin'], true)) {
        $message = 'Please choose a valid user type.';
        $message_type = 'danger';
    } elseif (!empty(getUserByEmail($formData['email_address']))) {
        $message = 'That email address is already registered.';
        $message_type = 'danger';
    } else {
        try {
            $pdo->beginTransaction();

            $userAdded = addUser(
                $formData['first_name'],
                $formData['last_name'],
                $formData['contact_number'],
                $formData['email_address'],
                $formData['user_type'],
                $password,
                $formData['security_question'],
                $formData['security_answer'],
                $formData['date_of_birth'] ?: null,
                $formData['gender'] ?: null,
                $formData['ethnicity'] ?: null
            );

            if (!$userAdded) {
                throw new Exception('Unable to create the user account.');
            }

            switch ($formData['user_type']) {
                case 'teacher':
                    if (!addTeacher($formData['email_address'])) {
                        throw new Exception('Unable to create the teacher profile.');
                    }
                    break;
                case 'parent':
                    if (!addParent($formData['email_address'])) {
                        throw new Exception('Unable to create the parent profile.');
                    }
                    break;
                case 'student':
                    if (!addStudent($formData['email_address'])) {
                        throw new Exception('Unable to create the student profile.');
                    }
                    break;
                case 'admin':
                    break;
            }

            $pdo->commit();
            header('Location: systemAdminstrator.php?status=success');
            exit();
        } catch (Throwable $exception) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            $message = $exception->getMessage() ?: 'Registration failed. Please try again.';
            $message_type = 'danger';
        }
    }
}

require_once "../view/systemAdminstratorView.php";
?>