<?php
session_start();
require_once "../model/user.php";
require_once "../model/teacher.php";
require_once "../model/dataAccess.php";

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$userType = strtolower($_SESSION['user_type'] ?? '');
if (!in_array($userType, ['student', 'parent', 'teacher', 'admin'], true)) {
    die("Access denied.");
}

$session_email  = $_SESSION['email_address'];
$bookings       = [];
$invoices       = [];
$current_booking_count  = 0;
$booking_identity_label = 'Showing bookings for: ' . $session_email;
$cancel_message = '';
$cancel_error   = '';
$feedback_message = '';
$feedback_error = '';

/* -------------------------------------------------
   FEEDBACK SUBMISSION (Teacher only)
   ------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_feedback'])) {
    if ($userType !== 'teacher') {
        die("Only teachers can submit feedback.");
    }

    $teacher_email   = $_POST['teacher_email']   ?? '';
    $feedback_type   = $_POST['feedback_type']   ?? ''; // 'student' or 'parent'
    $recipient_email = $_POST['recipient_email'] ?? '';
    $feedback_text   = trim($_POST['feedback_text'] ?? '');

    if (!empty($teacher_email) && !empty($recipient_email) && in_array($feedback_type, ['student', 'parent'], true)) {
        if ($feedback_type === 'student') {
            $result = submitStudentFeedback($teacher_email, $recipient_email, $feedback_text);
        } else {
            $result = submitParentFeedback($teacher_email, $recipient_email, $feedback_text);
        }

        if ($result) {
            $feedback_message = 'Feedback saved successfully.';
        } else {
            $feedback_error = 'Unable to save feedback. Please try again.';
        }
    } else {
        $feedback_error = 'Missing required feedback fields.';
    }

    header("Location: " . $_SERVER['PHP_SELF'] . ($feedback_message ? '?fb_ok=1' : '?fb_err=1'));
    exit();
}

/* -------------------------------------------------
   CANCEL BOOKING (Student / Parent only)
   ------------------------------------------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_slot_id'])) {
    if (!in_array($userType, ['student', 'parent'], true)) {
        die("Unauthorized.");
    }

    $slot_id = (int)$_POST['cancel_slot_id'];
    $cancelStudentEmail = $session_email;

    if ($userType === 'parent' && !empty($_POST['cancel_student_email'])) {
        $cancelStudentEmail = trim($_POST['cancel_student_email']);
    }

    $result = cancelBooking($slot_id, $cancelStudentEmail);

    if ($result === true) {
        $cancel_message = 'Booking cancelled successfully.';
    } elseif ($result === 'not_found') {
        $cancel_error = 'Booking not found or already cancelled.';
    } elseif ($result === 'not_owner') {
        $cancel_error = 'You can only cancel your own bookings.';
    } else {
        $cancel_error = 'Unable to cancel booking. Please try again.';
    }

    header("Location: " . $_SERVER['PHP_SELF'] . ($cancel_message ? '?cancelled=1' : '?error=1'));
    exit();
}

/* -------------------------------------------------
   FLASH MESSAGES
   ------------------------------------------------- */
if (isset($_GET['cancelled'])) {
    $cancel_message = 'Booking cancelled successfully.';
} elseif (isset($_GET['error'])) {
    $cancel_error = 'Unable to cancel booking. Please try again.';
}

if (isset($_GET['fb_ok'])) {
    $feedback_message = 'Feedback saved successfully.';
} elseif (isset($_GET['fb_err'])) {
    $feedback_error = 'Unable to save feedback. Please try again.';
}

/* -------------------------------------------------
   FETCH BOOKINGS
   ------------------------------------------------- */
if ($userType === 'parent') {
    $linkedStudents = getAllStudentsForParent($session_email);

    if (!empty($linkedStudents)) {
        $booking_identity_label = 'Showing bookings for ' . count($linkedStudents) . ' linked student(s)';

        foreach ($linkedStudents as $student) {
            $studentBookings = getCurrentBookingsForUser($student->email_address);

            foreach ($studentBookings as $booking) {
                $booking->student_first_name = $student->first_name;
                $booking->student_last_name  = $student->last_name;
                $booking->student_email      = $student->email_address;
                $booking->parent_email_address = $session_email;
            }

            $bookings = array_merge($bookings, $studentBookings);

            $studentInvoices = getInvoicesForStudent($student->email_address);
            foreach ($studentInvoices as $invoice) {
                $invoice->student_first_name = $student->first_name;
                $invoice->student_last_name  = $student->last_name;
            }
            $invoices = array_merge($invoices, $studentInvoices);
        }

        usort($bookings, function ($a, $b) {
            return strcmp($a->slot_date . $a->start_time, $b->slot_date . $b->start_time);
        });
    }
} elseif ($userType === 'student') {
    $bookings = getCurrentBookingsForUser($session_email);
    $invoices = getInvoicesForStudent($session_email);
} elseif ($userType === 'teacher') {
    $bookings = getTeacherBookedSlots($session_email);
    $booking_identity_label = 'Showing your booked lessons';

    $teacherUser = getUserByEmail($session_email)[0] ?? null;

    foreach ($bookings as $booking) {
        $booking->teacher_first_name   = $teacherUser->first_name ?? '';
        $booking->teacher_last_name    = $teacherUser->last_name ?? '';
        $booking->teacher_email_address = $session_email;
        $booking->student_email        = $booking->student_email_address ?? '';
        $booking->parent_email_address = getParentForStudent($booking->student_email_address);
    }
} elseif ($userType === 'admin') {
    $bookings = getAllCurrentBookings();
    $booking_identity_label = 'Admin view – all current bookings';

    foreach ($bookings as $booking) {
        $booking->student_email        = $booking->student_email_address ?? '';
        $booking->parent_email_address = getParentForStudent($booking->student_email_address);
    }
}

/* -------------------------------------------------
   ATTACH FEEDBACK TO EACH BOOKING
   ------------------------------------------------- */
foreach ($bookings as $booking) {
    $teacherEmail = $booking->teacher_email_address ?? '';
    $studentEmail = $booking->student_email ?? $booking->student_email_address ?? '';
    $parentEmail  = $booking->parent_email_address ?? '';

    if (!empty($teacherEmail)) {
        $booking->student_feedback_text = getStudentFeedback($teacherEmail, $studentEmail);
        $booking->parent_feedback_text  = getParentFeedback($teacherEmail, $parentEmail);
    }
}

$current_booking_count = count($bookings);

require_once "../view/bookingsView.php";