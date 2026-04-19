<?php
session_start();
require_once "../model/dataAccess.php";

if (!isset($_SESSION['email_address'])) {
    header("Location: sign_in.php");
    exit();
}

$session_email = $_SESSION['email_address'];
$userType      = $_SESSION['user_type'] ?? '';
$action        = $_POST['action'] ?? '';

try {
    if ($action === 'update_profile') {

        $first_name      = trim($_POST['first_name']      ?? '');
        $last_name       = trim($_POST['last_name']       ?? '');
        $contact_number  = trim($_POST['contact_number']  ?? '');
        $new_password    = trim($_POST['new_password']    ?? '');
        $confirm_password = trim($_POST['confirm_password'] ?? '');

        if ($first_name === '' || $last_name === '') {
            throw new Exception("First and last name are required.");
        }

        if ($new_password !== '' && $new_password !== $confirm_password) {
            throw new Exception("Passwords do not match.");
        }

        updateUserProfile($session_email, $first_name, $last_name, $contact_number, $new_password !== '' ? $new_password : null);
        $_SESSION['profile_success'] = "Profile updated successfully.";

    } elseif ($action === 'update_rate' && $userType === 'teacher') {

        $rate = trim($_POST['hourly_rate'] ?? '');
        if (!is_numeric($rate) || (float)$rate < 0) {
            throw new Exception("Please enter a valid hourly rate.");
        }
        updateTeacherRate($session_email, (float)$rate);
        $_SESSION['profile_success'] = "Hourly rate updated.";

    } elseif ($action === 'add_slot' && $userType === 'teacher') {

        $slot_date  = trim($_POST['slot_date']   ?? '');
        $start_time = trim($_POST['start_time']  ?? '');
        $end_time   = trim($_POST['end_time']    ?? '');

        if ($slot_date === '' || $start_time === '' || $end_time === '') {
            throw new Exception("Please fill in all slot fields.");
        }
        if ($slot_date < date('Y-m-d')) {
            throw new Exception("Slot date must be today or in the future.");
        }
        if ($start_time >= $end_time) {
            throw new Exception("End time must be after start time.");
        }

        addLessonSlot($session_email, $slot_date, $start_time, $end_time);
        $_SESSION['profile_success'] = "Lesson slot added successfully.";

    } else {
        throw new Exception("Invalid action.");
    }

} catch (Exception $e) {
    $_SESSION['profile_error'] = $e->getMessage();
}

header("Location: myprofile.php");
exit();
?>
