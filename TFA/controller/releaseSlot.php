<?php
session_start();
require_once "../model/dataAccess.php";

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php");
    exit();
}

if ($_SESSION['user_type'] !== 'teacher') {
    die("Access denied.");
}

$slot_id = $_POST['slot_id'] ?? null;
$teacher_email = $_SESSION['email_address'];

if (!$slot_id) {
    $_SESSION['booking_error'] = "No slot specified.";
    header("Location: teacherBookings.php");
    exit();
}

// Release the slot (make it available again)
$result = releaseTeacherSlot($slot_id, $teacher_email);

if ($result) {
    $_SESSION['booking_success'] = "Slot has been released and is now available for booking.";
} else {
    $_SESSION['booking_error'] = "Could not release the slot. It may have already been released.";
}

header("Location: teacherBookings.php");
exit();
