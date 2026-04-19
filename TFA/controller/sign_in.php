<?php
session_start();
session_regenerate_id(true);
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../model/user.php";
require_once "../model/dataAccess.php";

$requested_redirect = $_GET['redirect'] ?? $_POST['redirect'] ?? '';

// Helper: check if a logged-in student is missing a required parent
function studentRequiresParent($email) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT s.student_type, sp.parent_email_address 
        FROM students s 
        LEFT JOIN student_parent sp 
            ON LOWER(TRIM(s.email_address)) = LOWER(TRIM(sp.student_email_address))
        WHERE LOWER(TRIM(s.email_address)) = LOWER(TRIM(?))
        LIMIT 1
    ");
    $stmt->execute([trim($email)]);
    $info = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$info) return false;

    $type = strtolower(trim($info->student_type ?? ''));
    // Only non-college students without a parent need to link one
    return ($type !== 'colleges, sixth form' && empty($info->parent_email_address));
}

// Already logged in?
if (isset($_SESSION['email_address'])) {
    $current_user_type = strtolower(trim($_SESSION['user_type'] ?? ''));

    if ($current_user_type === 'student' && studentRequiresParent($_SESSION['email_address'])) {
        header("Location: linkparent.php");
        exit();
    }

    $default_destination = ($current_user_type === 'admin') ? 'userlist.php' : 'myprofile.php';
    header("Location: " . ($requested_redirect !== '' ? $requested_redirect : $default_destination));
    exit();
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_address = trim($_POST['email_address'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email_address === '' || $password === '') {
        $error = "Please enter both your email and password.";
    } else {
        $results = loginUser($email_address, $password);

        if (!empty($results)) {
            $user = $results[0];

            $_SESSION['email_address'] = $user->email_address;
            $_SESSION['user_type'] = strtolower(trim($user->user_type));

            // Parent enforcement for under-18 students
            if ($_SESSION['user_type'] === 'student' && studentRequiresParent($user->email_address)) {
                header("Location: linkparent.php");
                exit();
            }

            $default_destination = ($_SESSION['user_type'] === 'admin') ? 'userlist.php' : 'myprofile.php';
            $redirect = ($requested_redirect !== '') ? $requested_redirect : $default_destination;

            header("Location: " . $redirect);
            exit();
        } else {
            $error = "Invalid email or password. Please try again.";
        }
    }
}

require_once "../view/signin_view.php";
?>