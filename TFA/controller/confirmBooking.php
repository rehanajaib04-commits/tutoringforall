<?php
session_start();
require_once "../model/dataAccess.php";

// INITIALIZE VARIABLES FIRST
$success = false;
$slot_id       = $_POST['slot_id'] ?? null;
$teacher_email = trim($_POST['teacher_email'] ?? '');
$session_email = $_SESSION['email_address'] ?? '';
$userType = $_SESSION['user_type'] ?? '';
$booking_email = null;
$date = '';
$time = '';
$error_message = ''; // Initialize this too

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

if (!in_array($userType, ['student', 'parent'], true)) {
    $error_message = "Error: Only student or parent accounts can book lessons.";
} elseif ($booking_email === null || $booking_email === '') {
    $booking_email = resolveBookingEmail($session_email, $userType);
    if ($booking_email === null || $booking_email === '') {
        $error_message = $userType === 'parent'
            ? 'No linked student account was found for this parent account.'
            : 'Your account is not linked to a bookable student profile.';
    }
}

if (empty($error_message)) {
    if (!$slot_id) {
        $error_message = 'No lesson slot was selected.';
    } else {
        try {
            $bookedSlot = bookSlot($slot_id, $booking_email);

            if ($bookedSlot) {
                $success       = true;
                $teacher_email = $teacher_email !== '' ? $teacher_email : $bookedSlot->teacher_email_address;
                $date          = date('l, jS F Y', strtotime($bookedSlot->slot_date));
                $time          = date('H:i', strtotime($bookedSlot->start_time)) . ' - ' . date('H:i', strtotime($bookedSlot->end_time));
                
                // CREATE INVOICE
                $rate = getTeacherRate($teacher_email);
                if ($rate) {
                    $duration_hours = calculateDurationHours($bookedSlot->start_time, $bookedSlot->end_time);
                    $total = round($rate * $duration_hours, 2);
                    createInvoice($teacher_email, $booking_email, $bookedSlot->slot_date, $total);
                }
            } else {
                $error_message = 'That slot could not be booked — it may already be taken.';
            }
        } catch (Exception $e) {
            $error_message = 'Database error: ' . $e->getMessage();
        }
    }
}

require_once "../view/confirmBookingView.php";
?>