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
    die("Only student or parent accounts can view booked lesson slots.");
}

$session_email  = $_SESSION['email_address'];
$booking_email  = resolveBookingEmail($session_email, $userType);
$bookings       = $booking_email ? getCurrentBookingsForUser($booking_email) : [];
$current_booking_count  = count($bookings);
$booking_identity_label = $userType === 'parent' && $booking_email
    ? 'Showing bookings for linked student: ' . $booking_email
    : 'Showing bookings for: ' . $session_email;

require_once "../view/bookingsView.php";
?>