<?php
session_start();
require_once "../model/user.php";
require_once "../model/teacher.php";
require_once "../model/dataAccess.php";

$id = $_GET['id'] ?? $_GET['teacher_email'] ?? null;

$teacher = null;
$hourly_rate = null;
if ($id) {
    $teacher = getTeacherDetails($id);
    $hourly_rate = getTeacherRate($id);
}

if (!$teacher) {
    die("Teacher not found.");
}

require_once "../view/teacherProfileView.php";
?>