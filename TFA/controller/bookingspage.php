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
$bookings       = [];
$invoices       = [];
$current_booking_count  = 0;
$booking_identity_label = 'Showing bookings for: ' . $session_email;

if ($userType === 'parent') {
    // Get all students linked to this parent
    $linkedStudents = getAllStudentsForParent($session_email);
    
    if (!empty($linkedStudents)) {
        $booking_identity_label = 'Showing bookings for ' . count($linkedStudents) . ' linked student(s)';
        
        // Collect bookings for all students
        foreach ($linkedStudents as $student) {
            $studentBookings = getCurrentBookingsForUser($student->email_address);
            
            // Add student info to each booking object for the view
            foreach ($studentBookings as $booking) {
                $booking->student_first_name = $student->first_name;
                $booking->student_last_name = $student->last_name;
                $booking->student_email = $student->email_address;
            }
            
            $bookings = array_merge($bookings, $studentBookings);
            
            // Collect invoices for this student
            $studentInvoices = getInvoicesForStudent($student->email_address);
            foreach ($studentInvoices as $invoice) {
                $invoice->student_first_name = $student->first_name;
                $invoice->student_last_name = $student->last_name;
            }
            $invoices = array_merge($invoices, $studentInvoices);
        }
        
        // Sort bookings by date
        usort($bookings, function($a, $b) {
            return strcmp($a->slot_date . $a->start_time, $b->slot_date . $b->start_time);
        });
    }
} else {
    // Student viewing their own bookings
    $bookings = getCurrentBookingsForUser($session_email);
    $invoices = getInvoicesForStudent($session_email);
}

$current_booking_count = count($bookings);

require_once "../view/bookingsView.php";
?>