<?php

require_once "../model/dataAccess.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $contact_number = $_POST['contact_number'];
    $email_address = $_POST['email_address'];
    $user_type = $_POST['user_type'];
    $password = $_POST['password']; 
    $security_question = $_POST['security_question'];
    $security_answer = $_POST['security_answer'];

    // For students, validate parent exists first
    if ($user_type === 'student') {
        $parent_email = $_POST['parent_email'];
        if (!checkParentExists($parent_email)) {
            $error_message = "Parent email not found. Please ensure your parent registers first.";
            $user_type_selection = $user_type;
            require "../view/viewRegister.php";
            exit();
        }
    }

    $result = addUser($first_name, $last_name, $contact_number, $email_address, $user_type, $password, $security_question, $security_answer);

    if ($result) {
        // Add to specific user type table
        if ($user_type === 'teacher') {
            addTeacher($email_address);
        } elseif ($user_type === 'parent') {
            addParent($email_address);
        } elseif ($user_type === 'student') {
            addStudent($email_address);
            linkStudentParent($email_address, $parent_email);
        }

        header("Location: login.php?status=success");
        exit();
    } else {
        $error_message = "Registration failed. Please try again.";
        require "../view/viewRegister.php";
    }
} 

else {
    if (isset($_GET['type'])) {
        $user_type_selection = $_GET['type'];
        require "../view/viewRegister.php";
    } 
 
    else {
        require "../view/choosetype.php";
    }
}
?>