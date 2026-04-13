<?php
session_start();
require_once "../model/user.php";
require_once "../model/teacher.php";
require_once "../model/dataAccess.php";

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$userType = $_SESSION['user_type'] ?? '';
if (!in_array($userType, ['student', 'parent'], true)) {
    die("Only student or parent accounts can view booking details.");
}

$slot_id       = $_GET['slot_id'] ?? null;
$session_email = $_SESSION['email_address'];
$booking_email = resolveBookingEmail($session_email, $userType);

if (!$slot_id) {
    die("No booking specified.");
}

$slot = getSlotById($slot_id);

// Verify slot exists, is booked, and belongs to this student
if (!$slot || !$slot->is_booked) {
    die("Booking not found.");
}
if (strtolower(trim($slot->student_email_address)) !== strtolower(trim($booking_email))) {
    die("You are not authorised to view this booking.");
}

$teacher = getTeacherDetails($slot->teacher_email_address);
if (!$teacher) {
    die("Teacher not found.");
}

$display_date  = date('l, jS F Y', strtotime($slot->slot_date));
$display_start = date('H:i', strtotime($slot->start_time));
$display_end   = date('H:i', strtotime($slot->end_time));

require_once "../view/bookingDetailView.php";
?>