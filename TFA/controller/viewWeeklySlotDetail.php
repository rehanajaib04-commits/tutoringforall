<?php
session_start();
require_once "../model/user.php";
require_once "../model/teacher.php";
require_once "../model/dataAccess.php";

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$availability_id = $_GET['availability_id'] ?? null;
$teacher_email   = trim($_GET['teacher_email'] ?? '');

if (!$availability_id || $teacher_email === '') {
    die("Invalid request.");
}

$teacher = getTeacherDetails($teacher_email);
if (!$teacher) die("Teacher not found.");

$slot = getAvailabilitySlotById($availability_id);
if (!$slot || trim($slot->teacher_email_address) !== trim($teacher_email)) {
    die("Slot not found.");
}

$day_full = [
    'Mon' => 'Monday',   'Tue' => 'Tuesday',  'Wed' => 'Wednesday',
    'Thu' => 'Thursday', 'Fri' => 'Friday',   'Sat' => 'Saturday',
    'Sun' => 'Sunday'
];

$display_day   = $day_full[$slot->weekday] ?? $slot->weekday;
$display_date  = !empty($slot->next_slot_date)
    ? date('l, jS F Y', strtotime($slot->next_slot_date))
    : 'Next available week';
$display_start = date('H:i', strtotime($slot->start_time));
$display_end   = date('H:i', strtotime($slot->end_time));
$userType      = $_SESSION['user_type'] ?? '';
$canBook       = in_array($userType, ['student', 'parent'], true);

require_once "../view/weeklySlotDetailView.php";
?>