<?php

require_once "../model/user.php";
require_once "../model/dataAccess.php";

$message = "";
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
//} 
/*if (isset($_POST['addUser'])){
    $first_name = $_POST['first_name'];
   $last_name = $_POST['last_name'];
   $contact_number = $_POST['contact_number'];
   $email_address = $_POST['email_address'];
   $user_type = $_POST['user_type'];
   $password = $_POST['password'];
   $security_question = $_POST['security_question'];
   $security_answer = $_POST['security_answer'];

   if(addUser( $first_name,$last_name,$contact_number,$email_address,$user_type,$password,$security_question,$security_answer)){
    $message = " user added successfully!";
   }
   else{$message="error adding user";}

}*/
require_once "../view/systemAdminstratorView.php";
?>

