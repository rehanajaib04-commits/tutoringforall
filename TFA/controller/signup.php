<?php


require_once '../model/dataAccess.php';
require_once '../model/user.php';
require_once '../model/student.php';
require_once '../model/parent.php';

$error_message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    

    $student_first_name = trim($_POST['student_first_name'] ?? '');
    $student_last_name = trim($_POST['student_last_name'] ?? '');
    $student_contact = trim($_POST['student_contact_number'] ?? '');
    $student_email = trim($_POST['student_email_address'] ?? '');
    
    $parent_first_name = trim($_POST['first_name'] ?? '');
    $parent_last_name = trim($_POST['last_name'] ?? '');
    $parent_contact = trim($_POST['contact_number'] ?? '');
    $parent_email = trim($_POST['email_address'] ?? '');
    
    $password = $_POST['password'] ?? '';
    $security_question = $_POST['security_question'] ?? '';
    $security_answer = trim($_POST['security_answer'] ?? '');
    
 
    if (empty($student_first_name) || empty($student_last_name) || empty($student_email) ||
        empty($parent_first_name) || empty($parent_last_name) || empty($parent_email) ||
        empty($password) || empty($security_answer)) {
        
        $error_message = "All required fields must be filled.";
        
    } elseif (!filter_var($student_email, FILTER_VALIDATE_EMAIL) || 
              !filter_var($parent_email, FILTER_VALIDATE_EMAIL)) {
        
        $error_message = "Please enter valid email addresses.";
        
    } elseif (strcasecmp($student_email, $parent_email) === 0) {
        
        $error_message = "Student and parent must have different email addresses.";
        
    } else {

        $existing_student = getUserByEmail($student_email);
        $existing_parent = getUserByEmail($parent_email);
        
        if (!empty($existing_student)) {
            $error_message = "Student email is already registered.";
        } elseif (!empty($existing_parent)) {
            $error_message = "Parent email is already registered.";
        } else {
            
            try {

                $pdo->beginTransaction();
                

                $parent_created = addUser(
                    $parent_first_name,
                    $parent_last_name,
                    $parent_contact,
                    $parent_email,
                    'parent',
                    $password,
                    $security_question,
                    $security_answer
                );
                

                $student_created = addUser(
                    $student_first_name,
                    $student_last_name,
                    $student_contact,
                    $student_email,
                    'student',
                    $password,
                    $security_question,
                    $security_answer
                );
                
                if ($parent_created && $student_created) {
                    
                    
                    addParent($parent_email, null);
                    
                    
                    addStudent($student_email, null);
                    
             
                    linkStudentParent($student_email, $parent_email);
                    
                    $pdo->commit();
                    
                    
                    header("Location: sign_in.php");
                    exit();
                    
                } else {
                    $pdo->rollBack();
                    $error_message = "Failed to create accounts. Please check your information and try again.";
                }
                
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error_message = "Database error: Unable to complete registration. Please try again later.";
     
            } catch (Exception $e) {
                $pdo->rollBack();
                $error_message = "An unexpected error occurred. Please try again.";
            }
        }
    }
}


include '../view/signupView.php';
?>

