<?php
session_start();
require_once "../model/user.php";
require_once "../model/teacher.php";
require_once "../model/dataaccess.php";

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$slot_id       = $_GET['slot_id'] ?? null;
$teacher_email = trim($_GET['teacher_email'] ?? '');

if (!$slot_id || $teacher_email === '') {
    die("Invalid request.");
}

$teacher = getTeacherDetails($teacher_email);
if (!$teacher) {
    die("Teacher not found.");
}

$slot = getSlotById($slot_id);
if (!$slot || trim($slot->teacher_email_address) !== trim($teacher_email)) {
    die("Slot not found.");
}

if ($slot->is_booked) {
    die("This slot has already been booked.");
}

$display_day   = date('l', strtotime($slot->slot_date));      // ADD THIS LINE
$display_date  = date('l, jS F Y', strtotime($slot->slot_date));
$display_start = date('H:i', strtotime($slot->start_time));
$display_end   = date('H:i', strtotime($slot->end_time));
$userType      = $_SESSION['user_type'] ?? '';
$canBook       = in_array($userType, ['student', 'parent'], true);

require_once "../view/slotDetailView.php";
?>