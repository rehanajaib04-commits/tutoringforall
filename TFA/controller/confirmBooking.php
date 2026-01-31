<?php
session_start();
require_once "../model/dataAccess.php";


if (!isset($_SESSION['user_id'])) {
    die("Error: Please log in first to book a lesson.");
}

$booking_id = $_POST['booking_id'] ?? null;
$student_id = $_SESSION['user_id']; 

if ($booking_id) {
    $success = bookLesson($booking_id, $student_id);
    
    if ($success) {
        echo "<h1>Booking Successful!</h1>";
        echo "<a href='teacherlist.php'>Back to bookings</a>";
    } else {
        echo "<h1>Booking Failed. Slot might no longer be available.</h1>";
    }
} else {
    echo "Invalid request.";
}
?>