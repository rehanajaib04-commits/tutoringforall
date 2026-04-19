<?php
session_start();
require_once "../model/user.php";
require_once "../model/dataAccess.php";

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

if ($_SESSION['user_type'] !== 'teacher') {
    die("Access denied. This page is for teachers only.");
}

$session_email = $_SESSION['email_address'];

// Get all booked slots for this teacher with student details
$bookedSlots = getTeacherBookedSlots($session_email);
$bookings_count = count($bookedSlots);

// Get invoices for this teacher
$invoices = getInvoicesForTeacher($session_email);

require_once "../view/teacherBookingsView.php";
?>