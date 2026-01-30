<?php

session_start();
require_once '../model/user.php';
require_once '../model/dataAccess.php';
require_once '../model/parent.php';
require_once '../model/student.php';

//$message = '';

if (isset($_REQUEST['submit']))
    {
        $parent_first_name = $_REQUEST['parent_first_name'];
        $parent_last_name = $_REQUEST['parent_last_name'];
        $parent_contact_number = $_REQUEST['parent_contact_number'];
        $parent_email_address = $_REQUEST['parent_email_address'];
        $parent_user_type = $_REQUEST['parent_user_type'];
        $password = $_REQUEST['parent_password'];
        $parent_security_question = $_REQUEST['parent_security_question'];
        $parent_security_answer = $_REQUEST['parent_security_answer'];
        $parent_type = $_REQUEST['parent_type'];

        $student_first_name = $_REQUEST['student_first_name'];
        $student_last_name = $_REQUEST['student_last_name'];
        $student_contact_number = $_REQUEST['student_contact_number'];
        $student_email_address = $_REQUEST['student_email_address'];
        $student_user_type = $_REQUEST['student_user_type'];
        $student_password = $_REQUEST['student_password'];
        $student_security_question = $_REQUEST['student_security_question'];
        $student_security_answer = $_REQUEST['student_security_answer'];
        $student_type = $_REQUEST['student_type'];

        $user = new User();
        $user->first_name = htmlentities($parent_first_name);
        $user->last_name = htmlentities($parent_last_name);
        $user->contact_number = htmlentities($parent_contact_number);
        $user->email_address = htmlentities($parent_email_address);
        $user->user_type = htmlentities($parent_user_type);
        $user->password = htmlentities($password);
        $user->security_question = htmlentities($parent_security_question);
        $user->security_answer = htmlentities($parent_security_answer);

        $user2 = new User();
        $user2->first_name = htmlentities($student_first_name);
        $user2->last_name = htmlentities($student_last_name);       
        $user2->contact_number = htmlentities($student_contact_number);
        $user2->email_address = htmlentities($student_email_address);
        $user2->user_type = htmlentities($student_user_type);
        $user2->password = htmlentities($student_password);
        $user2->security_question = htmlentities($student_security_question);
        $user2->security_answer = htmlentities($student_security_answer);


        //addUser($user);  
        if (addUser($user)) {
            $message = "User added successfully.";
            $parent_typeObj = new ParentUser();
            $parent_typeObj->email_address = htmlentities($parent_email_address);
            $parent_typeObj->parent_type = htmlentities($parent_type);
            addParent($parent_typeObj);

        } else {
            $message = "Error adding user.";
        }

        if (addUser($user2)) {
            $message2 = "User added successfully.";
            $student_typeObj = new StudentUser();
            $student_typeObj->email_address = htmlentities($student_email_address);
            $student_typeObj->student_type = htmlentities($student_type);
            addStudent($student_typeObj);
            
        } else {
            $message2 = "Error adding user.";
        }

        studentParent($student_email_address, $parent_email_address);
}


require_once '../view/addParentStudent_view.php';