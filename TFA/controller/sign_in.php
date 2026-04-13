<?php
session_start();

require_once "../model/user.php";
require_once "../model/dataAccess.php";

// If already logged in, redirect to profile
if (isset($_SESSION['email_address'])) {
    header("Location: myprofile.php");
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

            // All user types now go to myprofile.php by default
            if ($user->user_type === 'teacher') {
                $default_destination = 'myprofile.php';
            } else {
                // Students and parents now land on their profile page
                $default_destination = 'myprofile.php';
            }

            // Priority: Specific redirect parameter > Role-based default
            $redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? $default_destination;
            
            header("Location: " . $redirect);
            exit();
        } else {
            $error = "Invalid email or password. Please try again.";
        }
    }
}

require_once "../view/signin_view.php";
?>