<?php
session_start();
require_once "../model/user.php";
require_once "../model/teacher.php";
require_once "../model/dataAccess.php";

$teacher_email = trim($_GET['teacher_email'] ?? $_GET['id'] ?? '');

if ($teacher_email === '') {
    die("No teacher specified.");
}

$teacher = getTeacherDetails($teacher_email);
if (!$teacher) {
    die("Teacher not found.");
}

$teacher_email = trim($teacher->email_address);

// Fetch all available (unbooked, future) slots for this teacher
$raw_slots = getTeacherAvailableSlots($teacher_email);

// Group slots by date for the view
$by_date = [];
foreach ($raw_slots as $slot) {
    $by_date[$slot->slot_date][] = $slot;
}

// Sort dates ascending
ksort($by_date);

$all_dates = array_keys($by_date);

// Default to first available date
$selected_date = trim($_GET['date'] ?? '');
if (!in_array($selected_date, $all_dates, true)) {
    $selected_date = $all_dates[0] ?? '';
}

$slots_today      = $by_date[$selected_date] ?? [];
$total_open_slots = count($raw_slots);

require_once "../view/availabilityView.php";
?>