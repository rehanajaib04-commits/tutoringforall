<?php
session_start();
require_once "../model/dataAccess.php";

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$userType = $_SESSION['user_type'] ?? '';
if (!in_array($userType, ['student', 'parent'], true)) {
    die("Error: Only student or parent accounts can cancel bookings.");
}

$slot_id       = $_POST['slot_id'] ?? null;
$session_email = $_SESSION['email_address'];
$success       = false;
$error_message = '';
$cancelled_slot = null;

if (!$slot_id) {
    $error_message = 'No lesson slot was specified.';
} else {
    // Get the slot details to check ownership
    $slot = getSlotById($slot_id);
    
    if (!$slot || !$slot->is_booked) {
        $error_message = 'That booking could not be found.';
    } else {
        $canCancel = false;
        
        if ($userType === 'student') {
            // Student can only cancel their own bookings
            if (strtolower(trim($slot->student_email_address)) === strtolower(trim($session_email))) {
                $canCancel = true;
                $booking_email = $session_email;
            } else {
                $error_message = 'You are not authorised to cancel this booking.';
            }
        } elseif ($userType === 'parent') {
            // Parent can cancel bookings for any of their linked students
            $linkedStudents = getAllStudentsForParent($session_email);
            $studentEmails = array_map(function($s) { 
                return strtolower(trim($s->email_address)); 
            }, $linkedStudents);
            
            $bookingStudentEmail = strtolower(trim($slot->student_email_address));
            
            if (in_array($bookingStudentEmail, $studentEmails)) {
                $canCancel = true;
                $booking_email = $slot->student_email_address; // Use the actual student's email
            } else {
                $error_message = 'You are not authorised to cancel this booking.';
            }
        }
        
        if ($canCancel) {
            try {
                $result = cancelBooking($slot_id, $booking_email);

                if ($result === true) {
                    $success = true;
                } elseif ($result === 'not_found') {
                    $error_message = 'That booking could not be found.';
                } elseif ($result === 'not_owner') {
                    $error_message = 'You are not authorised to cancel this booking.';
                } else {
                    $error_message = 'The booking could not be cancelled. Please try again.';
                }
            } catch (Exception $e) {
                $error_message = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

require_once "../view/cancelBookingView.php";
?>