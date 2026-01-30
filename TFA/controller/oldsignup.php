<?php
require_once "../model/user.php";
require_once "../model/dataAccess.php";

$message = "";

if (isset($_POST['signup'])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $contact_number = $_POST['contact_number'];
    $email_address = $_POST['email_address'];
    $user_type = $_POST['user_type']; 
    $password = $_POST['password']; 
    $security_question = $_POST['security_question'];
    $security_answer = $_POST['security_answer'];

    if (addUser($first_name, $last_name, $contact_number, $email_address, $user_type, $password, $security_question, $security_answer)) {
        $message = "Account created successfully! <a href='../view/signin_view.php'>Click here to sign in</a>";
    } else {
        $message = "Error creating account. The email might already be in use.";
    }
}

require_once "../view/signupView.php";
?>