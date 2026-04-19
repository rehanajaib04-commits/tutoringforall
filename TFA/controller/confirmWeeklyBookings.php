<?php
session_start();
require_once "../model/dataAccess.php";

// ... existing code ...

if ($booking_email === null || $booking_email === '') {
    // ... existing error handling ...
} elseif (!$availability_id) {
    // ... existing error handling ...
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
            
            // CREATE INVOICE for weekly booking
            $rate = getTeacherRate($teacher_email);
            if ($rate && !empty($bookedSlot->next_slot_date)) {
                $duration_hours = calculateDurationHours($bookedSlot->start_time, $bookedSlot->end_time);
                $total = round($rate * $duration_hours, 2);
                createInvoice($teacher_email, $booking_email, $bookedSlot->next_slot_date, $total);
            }
        } else {
            $error_message = 'That weekly slot could not be booked — it may already be taken.';
        }
    } catch (Exception $e) {
        $error_message = 'Database error: ' . $e->getMessage();
    }
}

require_once "../view/confirmWeeklyBookingView.php";
?>