<?php
require_once '../model/dataAccess.php';
require_once '../model/user.php';
require_once '../model/teacher.php';
require_once '../model/student.php';
require_once '../model/parent.php';

// TODO: Add admin authentication check here when ready
// session_start();
// if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
//     header("Location: sign_in.php");
//     exit();
// }

$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_type'])) {
    $user_type = $_POST['user_type'];
    
    // Common fields for all user types
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $email_address = trim($_POST['email_address'] ?? '');
    $password = $_POST['password'] ?? '';
    $security_question = $_POST['security_question'] ?? '';
    $security_answer = trim($_POST['security_answer'] ?? '');
    
    // Validation
    if (empty($first_name) || empty($last_name) || empty($email_address) || 
        empty($password) || empty($security_answer)) {
        $error_message = "All required fields must be filled.";
    } elseif (!filter_var($email_address, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        $existing_user = getUserByEmail($email_address);
        
        if (!empty($existing_user)) {
            $error_message = "Email address is already registered.";
        } else {
            try {
                $pdo->beginTransaction();
                
                // Create base user account
                $user_created = addUser(
                    $first_name,
                    $last_name,
                    $contact_number,
                    $email_address,
                    $user_type,
                    $password,
                    $security_question,
                    $security_answer
                );
                
                if ($user_created) {
                    // Handle type-specific details
                    switch($user_type) {
                        case 'teacher':
                            $teacher_type = $_POST['teacher_type'] ?? null;
                           /* $bio = trim($_POST['bio'] ?? '');
                            $qualifications = trim($_POST['qualifications'] ?? '');
                            $subjects = trim($_POST['subjects'] ?? '');*/
                            
                            addTeacher($email_address, $teacher_type); //$bio, $qualifications, $subjects);
                            break;
                            
                        case 'student':
                            $student_type = $_POST['student_type'] ?? null;
                            addStudent($email_address, $student_type);
                            
                            // Link to parent if selected
                            if (!empty($_POST['parent_email'])) {
                                $parent_email = $_POST['parent_email'];
                                if (checkParentExists($parent_email)) {
                                    linkStudentParent($email_address, $parent_email);
                                }
                            }
                            break;
                            
                        case 'parent':
                            $parent_type = $_POST['parent_type'] ?? null;
                            addParent($email_address, $parent_type);
                            
                            // Add address details
                            $postcode = trim($_POST['post_code'] ?? '');
                            $addressline1 = trim($_POST['address_line1'] ?? '');
                            $addressline2 = trim($_POST['address_line2'] ?? '');
                            $town = trim($_POST['town'] ?? '');
                            $county = trim($_POST['county'] ?? '');
                            
                            if (!empty($postcode) && !empty($addressline1)) {
                                addAddress($postcode, $addressline1, $addressline2, $town, $county);
                                linkUserAddress($email_address, $addressline1, $postcode);
                            }
                            break;
                            
                        case 'admin':
                            // Admin only needs the base user record, no additional tables
                            break;
                    }
                    
                    $pdo->commit();
                    $success_message = ucfirst($user_type) . " account created successfully for " . htmlspecialchars($email_address);
                    
                    // Optionally redirect after success
                    // header("Location: adminAddUser.php?success=1");
                    // exit();
                } else {
                    $pdo->rollBack();
                    $error_message = "Failed to create user account.";
                }
                
            } catch (PDOException $e) {
                $pdo->rollBack();
                $error_message = "Database error: Unable to complete registration. " . $e->getMessage();
            } catch (Exception $e) {
                $pdo->rollBack();
                $error_message = "An unexpected error occurred. Please try again.";
            }
        }
    }
}

// Fetch parents list if we're showing the student form
$parents_list = [];
if (isset($_GET['step']) && $_GET['step'] == '2' && isset($_GET['type']) && $_GET['type'] == 'student') {
    $parents_list = getAllParents();
}

include '../view/adminAddUser_view.php';
?>