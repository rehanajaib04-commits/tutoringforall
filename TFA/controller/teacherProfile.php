<?php
require_once "../model/user.php";
require_once "../model/teacher.php";
require_once "../model/dataAccess.php";

// Get ID from URL
$id = $_GET['id'] ?? null;

if ($id) {
    $teacher = getTeacherDetails($id);
}

if (!$teacher) {
    die("Teacher not found.");
}

require_once "../view/teacherProfileView.php";
?>