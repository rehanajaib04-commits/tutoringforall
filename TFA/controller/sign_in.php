<?php
session_start();

require_once "../model/user.php";
require_once "../model/dataAccess.php";


if (isset($_SESSION['email_address'])) {
    header("Location: teacherlist.php");
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email_address = trim($_POST['email_address'] ?? '');
    $password      = $_POST['password'] ?? '';

    if ($email_address === '' || $password === '') {
        $error = "Please enter both your email and password.";
    } else {
        $results = loginUser($email_address, $password);

        if (!empty($results)) {
            $user = $results[0];
            $_SESSION['email_address'] = $user->email_address;
            $_SESSION['user_type']     = $user->user_type;

            // Go back to the page they were trying to reach, or default to teacherlist
            $redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '../controller/teacherlist.php';
            header("Location: " . $redirect);
            exit();
        } else {
            $error = "Invalid email or password. Please try again.";
        }
    }
}

require_once "../view/signin_view.php";
?>