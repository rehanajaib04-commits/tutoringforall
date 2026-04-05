<?php
session_start();
require_once "../model/dataAccess.php";

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$userType = $_SESSION['user_type'] ?? '';
if (!in_array($userType, ['student', 'parent'], true)) {
    die("Error: Only student or parent accounts can book lessons.");
}

$availability_id = $_POST['availability_id'] ?? null;
$teacher_email   = trim($_POST['teacher_email'] ?? '');
$session_email   = $_SESSION['email_address'];
$booking_email   = resolveBookingEmail($session_email, $userType);
$success         = false;
$date            = '';
$time            = '';
$day             = '';
$error_message   = '';

if ($booking_email === null || $booking_email === '') {
    $error_message = $userType === 'parent'
        ? 'No linked student account was found for this parent account.'
        : 'Your account is not linked to a bookable student profile.';
} elseif (!$availability_id) {
    $error_message = 'No lesson slot was selected.';
} else {
    try {
        $bookedSlot = bookWeeklySlot($availability_id, $booking_email);

        if ($bookedSlot) {
            $success       = true;
            $teacher_email = $teacher_email !== '' ? $teacher_email : $bookedSlot->teacher_email;
            $day_full      = ['Mon'=>'Monday','Tue'=>'Tuesday','Wed'=>'Wednesday','Thu'=>'Thursday','Fri'=>'Friday','Sat'=>'Saturday','Sun'=>'Sunday'];
            $day           = $day_full[$bookedSlot->day_of_week] ?? $bookedSlot->day_of_week;
            $date          = !empty($bookedSlot->next_slot_date)
                ? date('l, jS F Y', strtotime($bookedSlot->next_slot_date))
                : $day;
            $time          = date('H:i', strtotime($bookedSlot->start_time)) . ' - ' . date('H:i', strtotime($bookedSlot->end_time));
        } else {
            $error_message = 'That weekly slot could not be booked — it may already be taken.';
        }
    } catch (Exception $e) {
        $error_message = 'Database error: ' . $e->getMessage();
    }
}

require_once "../view/confirmWeeklyBookingView.php";
?>