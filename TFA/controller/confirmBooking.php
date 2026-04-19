<?php
session_start();
require_once "../model/dataAccess.php";

// INITIALIZE VARIABLES
$success = false;
$error_message = '';

$slot_id       = $_POST['slot_id'] ?? null;
$teacher_email = trim($_POST['teacher_email'] ?? '');
$session_email = $_SESSION['email_address'] ?? '';
$userType      = strtolower($_SESSION['user_type'] ?? '');

$booking_email = null;
$date = '';
$time = '';

// Redirect if not logged in
if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// 🔐 Determine who the booking is for
if ($userType === 'student') {

    $booking_email = $session_email;

} elseif ($userType === 'parent') {

    $selected_student = $_POST['student_email'] ?? '';

    if (!$selected_student) {
        $error_message = 'Please select a child.';
    } else {

        // 🔐 SECURITY CHECK
        $linkedStudents = getAllStudentsForParent($session_email);

        $validEmails = array_map(function($s) {
            return strtolower(trim($s->email_address));
        }, $linkedStudents);

        if (!in_array(strtolower(trim($selected_student)), $validEmails)) {
            $error_message = 'Invalid student selection.';
        } else {
            $booking_email = $selected_student;
        }
    }

} else {
    $error_message = "Error: Only student or parent accounts can book lessons.";
}

// 🚀 Proceed with booking
if (empty($error_message)) {

    if (!$slot_id) {
        $error_message = 'No lesson slot was selected.';
    } else {
        try {
            $bookedSlot = bookSlot($slot_id, $booking_email);

            if ($bookedSlot) {
                $success = true;

                $teacher_email = $teacher_email !== '' 
                    ? $teacher_email 
                    : $bookedSlot->teacher_email_address;

                $date = date('l, jS F Y', strtotime($bookedSlot->slot_date));
                $time = date('H:i', strtotime($bookedSlot->start_time)) . ' - ' . date('H:i', strtotime($bookedSlot->end_time));

                // 💰 CREATE INVOICE
                $rate = getTeacherRate($teacher_email);
                if ($rate) {
                    $duration_hours = calculateDurationHours($bookedSlot->start_time, $bookedSlot->end_time);
                    $total = round($rate * $duration_hours, 2);

                    createInvoice(
                        $teacher_email,
                        $booking_email,
                        $bookedSlot->slot_date,
                        $total
                    );
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