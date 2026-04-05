<?php

require_once "user.php";
require_once "bookings.php";

$username = "root";
$password = "";
$db = "tfa1";
$servername = "localhost";

$pdo = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function addUser($first_name, $last_name, $contact_number, $email_address, $user_type, $password, $security_question, $security_answer) {
    global $pdo;
    $statement = $pdo->prepare("INSERT INTO users (email_address, first_name, last_name, contact_number, user_type, password, security_question, security_answer) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    return $statement->execute([
        $email_address, $first_name, $last_name, $contact_number,
        $user_type, $password, $security_question, $security_answer
    ]);
}

function loginUser($email_address, $password){
    global $pdo;
    $statement = $pdo->prepare('SELECT * FROM users WHERE email_address = ? AND password = ?');
    $statement->execute([$email_address, $password]);
    return $statement->fetchAll(PDO::FETCH_CLASS, 'User');
}

function getUserByEmail($email_address) {
    global $pdo;
    $statement = $pdo->prepare('SELECT * FROM users WHERE email_address = ?');
    $statement->execute([$email_address]);
    return $statement->fetchAll(PDO::FETCH_CLASS, 'User');
}

function addTeacher($email_address, $teacher_type = null) {
    global $pdo;
    $statement = $pdo->prepare("INSERT INTO teachers (email_address, teacher_type) VALUES (?, ?)");
    return $statement->execute([$email_address, $teacher_type]);
}

function addStudent($email_address, $student_type = null) {
    global $pdo;
    $statement = $pdo->prepare("INSERT INTO students (email_address, student_type) VALUES (?, ?)");
    return $statement->execute([$email_address, $student_type]);
}

function addParent($email_address, $parent_type = null) {
    global $pdo;
    $statement = $pdo->prepare("INSERT INTO parents (email_address, parent_type) VALUES (?, ?)");
    return $statement->execute([$email_address, $parent_type]);
}

function addAddress($postcode, $addressline1, $addressline2, $town, $county = null) {
    global $pdo;
    $statement = $pdo->prepare("INSERT INTO address1 (post_code, address_line1, addressline2, town, county) VALUES (?, ?, ?, ?, ?)");
    return $statement->execute([$postcode, $addressline1, $addressline2, $town, $county]);
}

function linkStudentParent($student_email, $parent_email) {
    global $pdo;
    $statement = $pdo->prepare("INSERT INTO student_parent (student_email_address, parent_email_address) VALUES (?, ?)");
    return $statement->execute([$student_email, $parent_email]);
}

function linkUserAddress($email_address, $addressline1, $postcode) {
    global $pdo;
    $statement = $pdo->prepare("INSERT INTO user_address (email_address, address_line1, post_code) VALUES (?, ?, ?)");
    return $statement->execute([$email_address, $addressline1, $postcode]);
}

function checkParentExists($email_address) {
    global $pdo;
    $statement = $pdo->prepare('SELECT * FROM parents WHERE email_address = ?');
    $statement->execute([$email_address]);
    return count($statement->fetchAll()) > 0;
}

function getAllParents() {
    global $pdo;
    $statement = $pdo->prepare('SELECT u.email_address, u.first_name, u.last_name 
                                FROM users u 
                                JOIN parents p ON u.email_address = p.email_address 
                                WHERE u.user_type = "parent"
                                ORDER BY u.last_name, u.first_name');
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getLinkedStudentEmailForParent($parentEmailAddress) {
    global $pdo;
    $statement = $pdo->prepare(
        "SELECT student_email_address FROM student_parent
         WHERE LOWER(TRIM(parent_email_address)) = LOWER(TRIM(?))
         ORDER BY student_email_address LIMIT 1"
    );
    $statement->execute([trim($parentEmailAddress)]);
    $studentEmail = $statement->fetchColumn();
    return $studentEmail ? trim($studentEmail) : null;
}

function resolveBookingEmail($userEmailAddress, $userType) {
    $userEmailAddress = trim($userEmailAddress);
    if ($userType === 'student') {
        return $userEmailAddress;
    }
    if ($userType === 'parent') {
        return getLinkedStudentEmailForParent($userEmailAddress);
    }
    return null;
}

function ensureStudentRecord($email_address) {
    global $pdo;
    $check = $pdo->prepare("SELECT email_address FROM students WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?))");
    $check->execute([trim($email_address)]);
    if (!$check->fetch()) {
        $insert = $pdo->prepare("INSERT INTO students (email_address, student_type) VALUES (?, NULL)");
        $insert->execute([trim($email_address)]);
    }
}

function addTeacherDetailed($email_address, $teacher_type, $bio, $qualifications, $subjects) {
    global $pdo;
    $statement = $pdo->prepare("INSERT INTO teachers (email_address, teacher_type) VALUES (?, ?)");
    return $statement->execute([$email_address, $teacher_type]);
}

function getAllTeacher() {
    global $pdo;
    $statement = $pdo->prepare(
        "SELECT t.email_address, t.teacher_type, u.first_name, u.last_name, u.contact_number
         FROM teachers t
         INNER JOIN users u ON u.email_address = t.email_address
         ORDER BY u.last_name, u.first_name"
    );
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_OBJ);
}

function getTeacherByName($full_name) {
    global $pdo;
    $search = '%' . $full_name . '%';
    $statement = $pdo->prepare(
        "SELECT t.email_address, t.teacher_type, u.first_name, u.last_name, u.contact_number
         FROM teachers t
         INNER JOIN users u ON u.email_address = t.email_address
         WHERE CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) LIKE ?
         ORDER BY u.last_name, u.first_name"
    );
    $statement->execute([$search]);
    return $statement->fetchAll(PDO::FETCH_OBJ);
}

function getTeacherDetails($teacherEmailAddress) {
    global $pdo;
    $statement = $pdo->prepare(
        "SELECT t.email_address, t.teacher_type, u.first_name, u.last_name, u.contact_number
         FROM teachers t
         INNER JOIN users u ON u.email_address = t.email_address
         WHERE t.email_address = ?"
    );
    $statement->execute([$teacherEmailAddress]);
    return $statement->fetchObject();
}

// --- lesson_slots functions ---

function getTeacherAvailableSlots($teacherEmailAddress) {
    global $pdo;
    $statement = $pdo->prepare(
        "SELECT slot_id, teacher_email_address, slot_date, start_time, end_time, is_booked
         FROM lesson_slots
         WHERE LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?))
           AND is_booked = 0
           AND slot_date >= CURDATE()
         ORDER BY slot_date, start_time"
    );
    $statement->execute([trim($teacherEmailAddress)]);
    return $statement->fetchAll(PDO::FETCH_OBJ);
}

function getSlotById($slot_id) {
    global $pdo;
    $statement = $pdo->prepare(
        "SELECT slot_id, teacher_email_address, slot_date, start_time, end_time, is_booked, student_email_address
         FROM lesson_slots
         WHERE slot_id = ?"
    );
    $statement->execute([$slot_id]);
    return $statement->fetch(PDO::FETCH_OBJ);
}

function bookSlot($slot_id, $studentEmailAddress) {
    global $pdo;

    // Step 1: fetch the slot and confirm it exists and is not already booked
    $stmt = $pdo->prepare(
        "SELECT slot_id, teacher_email_address, slot_date, start_time, end_time, is_booked
         FROM lesson_slots WHERE slot_id = ?"
    );
    $stmt->execute([$slot_id]);
    $slot = $stmt->fetch(PDO::FETCH_OBJ);

    if (!$slot) {
        return false; // slot doesn't exist
    }

    if ($slot->is_booked) {
        return false; // already booked
    }

    // Step 2: ensure student row exists (satisfies FK if present)
    ensureStudentRecord($studentEmailAddress);

    // Step 3: mark the slot as booked
    $updateStmt = $pdo->prepare(
        "UPDATE lesson_slots
         SET is_booked = 1, student_email_address = ?
         WHERE slot_id = ? AND is_booked = 0"
    );
    $updateStmt->execute([trim($studentEmailAddress), $slot_id]);

    if ($updateStmt->rowCount() === 0) {
        return false; // another request booked it first
    }

    // Step 4: return the full slot with teacher name
    $resultStmt = $pdo->prepare(
        "SELECT ls.slot_id, ls.teacher_email_address, ls.slot_date, ls.start_time, ls.end_time,
                ls.is_booked, ls.student_email_address,
                u.first_name AS teacher_first_name, u.last_name AS teacher_last_name
         FROM lesson_slots ls
         INNER JOIN users u ON u.email_address = ls.teacher_email_address
         WHERE ls.slot_id = ?"
    );
    $resultStmt->execute([$slot_id]);
    return $resultStmt->fetch(PDO::FETCH_OBJ);
}

function getCurrentBookingsForUser($studentEmailAddress) {
    global $pdo;
    $statement = $pdo->prepare(
        "SELECT ls.slot_id, ls.teacher_email_address, ls.slot_date, ls.start_time, ls.end_time,
                ls.is_booked, ls.student_email_address,
                u.first_name AS teacher_first_name, u.last_name AS teacher_last_name,
                t.teacher_type
         FROM lesson_slots ls
         INNER JOIN users u ON u.email_address = ls.teacher_email_address
         LEFT JOIN teachers t ON t.email_address = ls.teacher_email_address
         WHERE LOWER(TRIM(ls.student_email_address)) = LOWER(TRIM(?))
           AND ls.is_booked = 1
         ORDER BY ls.slot_date, ls.start_time"
    );
    $statement->execute([trim($studentEmailAddress)]);
    return $statement->fetchAll(PDO::FETCH_OBJ);
}
?>