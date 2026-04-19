<?php
session_start();
require_once "../model/user.php";
require_once "../model/dataAccess.php";

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$userType = strtolower(trim($_SESSION['user_type'] ?? ''));
$session_email = trim($_SESSION['email_address']);

if ($userType !== 'teacher') {
    die("Only teachers can access this page.");
}

$slot_id = (int)($_GET['slot_id'] ?? 0);

if ($slot_id <= 0) {
    die("Invalid booking.");
}

// Fetch the booking + invoice, verifying the teacher owns it
$stmt = $pdo->prepare("
    SELECT 
        ls.slot_id, ls.teacher_email_address, ls.slot_date, 
        ls.start_time, ls.end_time, ls.is_booked, ls.student_email_address,
        u.first_name AS student_first_name, u.last_name AS student_last_name,
        u.contact_number AS student_contact,
        i.invoice_number, i.Total AS invoice_total, i.status AS invoice_status,
        t.hourly_rate
    FROM lesson_slots ls
    INNER JOIN users u ON u.email_address = ls.student_email_address
    LEFT JOIN teachers t ON t.email_address = ls.teacher_email_address
    LEFT JOIN invoices i ON i.teacher_email_address = ls.teacher_email_address
                        AND i.student_email_address = ls.student_email_address
                        AND i.invoice_date = ls.slot_date
    WHERE ls.slot_id = ? 
      AND LOWER(TRIM(ls.teacher_email_address)) = LOWER(TRIM(?))
      AND ls.is_booked = 1
    LIMIT 1
");
$stmt->execute([$slot_id, $session_email]);
$booking = $stmt->fetch(PDO::FETCH_OBJ);

if (!$booking) {
    die("Booking not found or you do not have permission.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_payment') {
        $new_status = $_POST['status'] ?? '';

        if (!in_array($new_status, ['paid', 'unpaid', 'overdue'])) {
            $_SESSION['profile_error'] = "Invalid payment status.";
            header("Location: teacherBookingDetail.php?slot_id=" . $slot_id);
            exit();
        }

        if (!empty($booking->invoice_number)) {
            updateInvoiceStatus($booking->invoice_number, $new_status);
        } else {
            // No invoice yet — create one first
            $duration = calculateDurationHours($booking->start_time, $booking->end_time);
            $rate = (float)($booking->hourly_rate ?? 0);
            $total = $rate * $duration;

            createInvoice($session_email, $booking->student_email_address, $booking->slot_date, $total);

            // Grab the new invoice number and set its status
            $invStmt = $pdo->prepare("
                SELECT invoice_number FROM invoices 
                WHERE teacher_email_address = ? 
                  AND student_email_address = ? 
                  AND invoice_date = ? 
                ORDER BY invoice_number DESC LIMIT 1
            ");
            $invStmt->execute([$session_email, $booking->student_email_address, $booking->slot_date]);
            $newInv = $invStmt->fetch(PDO::FETCH_OBJ);

            if ($newInv) {
                updateInvoiceStatus($newInv->invoice_number, $new_status);
            }
        }

        $_SESSION['profile_success'] = "Payment status updated to " . ucfirst($new_status) . ".";
        header("Location: teacherBookingDetail.php?slot_id=" . $slot_id);
        exit();
    }

    if ($action === 'cancel_booking') {
        if (releaseTeacherSlot($slot_id, $session_email)) {
            $_SESSION['profile_success'] = "Booking cancelled successfully.";
        } else {
            $_SESSION['profile_error'] = "Failed to cancel booking.";
        }
        header("Location: myprofile.php#bookings");
        exit();
    }
}

$display_date  = date('l, jS F Y', strtotime($booking->slot_date));
$display_start = date('H:i', strtotime($booking->start_time));
$display_end   = date('H:i', strtotime($booking->end_time));
$duration      = calculateDurationHours($booking->start_time, $booking->end_time);
$hourly_rate   = (float)($booking->hourly_rate ?? 0);
$total_due     = $hourly_rate * $duration;

require_once "../view/teacherBookingDetailView.php";