<?php
session_start();

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=userDetails.php");
    exit();
}

if (strtolower(trim($_SESSION['user_type'] ?? '')) !== 'admin') {
    header("Location: myprofile.php");
    exit();
}

require_once "../model/user.php";
require_once "../model/dataAccess.php";

$email = trim($_GET['email'] ?? '');
if ($email === '') {
    header("Location: userlist.php");
    exit();
}

// Fetch basic user data
$users = getUserByEmail($email);
if (empty($users)) {
    $error = "User not found.";
    $user = null;
} else {
    $user = $users[0]; // getUserByEmail returns array of User objects
}

// Optionally fetch additional role-specific data
$teacherDetails = null;
$studentDetails = null;
$parentDetails = null;
$linkedStudents = [];

if ($user) {
    if ($user->user_type === 'teacher') {
        $teacherDetails = getTeacherDetails($user->email_address);
    } elseif ($user->user_type === 'student') {
        // You might want to fetch student type from students table
        global $pdo;
        $stmt = $pdo->prepare("SELECT student_type FROM students WHERE email_address = ?");
        $stmt->execute([$user->email_address]);
        $studentDetails = $stmt->fetch(PDO::FETCH_OBJ);
    } elseif ($user->user_type === 'parent') {
        global $pdo;
        $stmt = $pdo->prepare("SELECT parent_type FROM parents WHERE email_address = ?");
        $stmt->execute([$user->email_address]);
        $parentDetails = $stmt->fetch(PDO::FETCH_OBJ);
        // Get linked students
        $linkedStudents = getAllStudentsForParent($user->email_address);
    }
}

require_once "../view/userDetailsView.php";
?>