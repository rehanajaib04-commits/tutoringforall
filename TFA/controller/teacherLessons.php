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
$message = '';
$error = '';

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_booking_status'])) {
        $lesson_id = $_POST['lesson_id'] ?? 0;
        $new_status = $_POST['new_status'] ?? '';
        
        // Verify this booking belongs to this teacher
        $booking = getBookingById($lesson_id);
        if ($booking && strtolower(trim($booking->teacher_email_address)) === strtolower(trim($session_email))) {
            if (updateBookingStatus($lesson_id, $new_status)) {
                $message = "Booking status updated successfully.";
            } else {
                $error = "Failed to update booking status.";
            }
        } else {
            $error = "You are not authorized to update this booking.";
        }
    }
    
    if (isset($_POST['update_invoice_status'])) {
        $invoice_number = $_POST['invoice_number'] ?? 0;
        $new_status = $_POST['new_status'] ?? '';
        
        // Verify this invoice belongs to this teacher via the booking
        if (updateInvoiceStatus($invoice_number, $new_status)) {
            $message = "Invoice status updated successfully.";
        } else {
            $error = "Failed to update invoice status.";
        }
    }
}

$bookings = getTeacherBookingsWithInvoices($session_email);

require_once "../view/teacherLessonsView.php";
?>