<?php
session_start();
require_once "../model/user.php";
require_once "../model/teacher.php";
require_once "../model/bookings.php"; 
require_once "../model/dataAccess.php";

// Standardize on 'id' or 'teacher_id' - I'll use 'teacher_id' to match your logic
$teacher_id = $_GET['teacher_id'] ?? $_GET['id'] ?? null; 

$teacher = null;
$raw_availability = [];

if ($teacher_id) {
    $teacher = getTeacherDetails($teacher_id);
    $raw_availability = getTeacherAvailability($teacher_id);
}

if (!$teacher) {
    die("Teacher not found.");
}

$availability = [];
if ($raw_availability) {
    foreach ($raw_availability as $slot) {
        // Use the object properties from the Booking class
        $dateKey = date('l, jS F', strtotime($slot->date));
        $timeValue = date('H:i', strtotime($slot->start_time));
        $availability[$dateKey][] = $timeValue;
    }
}

require_once "../view/bookingsView.php";
?>