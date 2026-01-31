<?php
session_start();

require_once "../model/user.php";
require_once "../model/dataAccess.php";

$error = "";

if (isset($_POST['submit'])) {
    if (isset($_POST['email_address']) && isset($_POST['password']) && 
        $_POST['email_address'] != "" && $_POST['password'] != "") {
        
        $email_address = $_POST['email_address'];
        $password = $_POST['password'];
        
        // Use loginUser function to validate both email_address and password
        $results = loginUser($email_address, $password);
        
        if (!empty($results) && count($results) > 0) {
            // User found and password matches
            $user = $results[0];
            $_SESSION['email_address'] = $user->email_address;
            $_SESSION['user_type'] = $user->user_type;
            $_SESSION['user_id'] = $user->user_id;
            
            // Redirect to success page
            header("Location: ../controller/teacherlist.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please enter both username and password.";
    }
}

require_once "../view/signin_view.php"
?>