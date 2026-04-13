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

    $pdo->beginTransaction();
    
    try {
        // Step 3: mark the slot as booked
        $updateStmt = $pdo->prepare(
            "UPDATE lesson_slots
             SET is_booked = 1, student_email_address = ?
             WHERE slot_id = ? AND is_booked = 0"
        );
        $updateStmt->execute([trim($studentEmailAddress), $slot_id]);

        if ($updateStmt->rowCount() === 0) {
            $pdo->rollBack();
            return false; // another request booked it first
        }

        // Step 4: insert into bookings table (NEW)
        $bookingStmt = $pdo->prepare(
            "INSERT INTO bookings (slot_id, student_email_address, status) 
             VALUES (?, ?, 'scheduled')"
        );
        $bookingStmt->execute([$slot_id, trim($studentEmailAddress)]);

        $pdo->commit();

        // Step 5: return the full slot with teacher name
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
        
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
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

// --- Profile update functions ---

function updateUserProfile($email_address, $first_name, $last_name, $contact_number, $new_password = null) {
    global $pdo;
    if ($new_password !== null) {
        $stmt = $pdo->prepare(
            "UPDATE users SET first_name = ?, last_name = ?, contact_number = ?, password = ?
             WHERE email_address = ?"
        );
        $stmt->execute([$first_name, $last_name, $contact_number, $new_password, $email_address]);
    } else {
        $stmt = $pdo->prepare(
            "UPDATE users SET first_name = ?, last_name = ?, contact_number = ?
             WHERE email_address = ?"
        );
        $stmt->execute([$first_name, $last_name, $contact_number, $email_address]);
    }
}

function updateTeacherRate($email_address, $hourly_rate) {
    global $pdo;
    // Add hourly_rate column if it doesn't exist yet (safe to run multiple times)
    $pdo->exec("ALTER TABLE teachers ADD COLUMN IF NOT EXISTS hourly_rate DECIMAL(8,2) DEFAULT NULL");
    $stmt = $pdo->prepare("UPDATE teachers SET hourly_rate = ? WHERE email_address = ?");
    $stmt->execute([$hourly_rate, $email_address]);
}

function getTeacherRate($email_address) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT hourly_rate FROM teachers WHERE email_address = ?");
        $stmt->execute([$email_address]);
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        return $row ? $row->hourly_rate : null;
    } catch (Exception $e) {
        return null;
    }
}

function addLessonSlot($teacher_email, $slot_date, $start_time, $end_time) {
    global $pdo;
    $stmt = $pdo->prepare(
        "INSERT INTO lesson_slots (teacher_email_address, slot_date, start_time, end_time, is_booked)
         VALUES (?, ?, ?, ?, 0)"
    );
    return $stmt->execute([$teacher_email, $slot_date, $start_time, $end_time]);
}

function getTeacherAllSlots($teacherEmailAddress) {
    global $pdo;
    $statement = $pdo->prepare(
        "SELECT slot_id, teacher_email_address, slot_date, start_time, end_time, is_booked, student_email_address
         FROM lesson_slots
         WHERE LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?))
           AND slot_date >= CURDATE()
         ORDER BY slot_date, start_time"
    );
    $statement->execute([trim($teacherEmailAddress)]);
    return $statement->fetchAll(PDO::FETCH_OBJ);
}

// Also include cancelBooking if not already present
if (!function_exists('cancelBooking')) {
    function cancelBooking($slot_id, $studentEmailAddress) {
        global $pdo;
        $stmt = $pdo->prepare(
            "SELECT slot_id, is_booked, student_email_address FROM lesson_slots WHERE slot_id = ?"
        );
        $stmt->execute([$slot_id]);
        $slot = $stmt->fetch(PDO::FETCH_OBJ);
        if (!$slot || !$slot->is_booked) return 'not_found';
        if (strtolower(trim($slot->student_email_address)) !== strtolower(trim($studentEmailAddress))) return 'not_owner';
        $update = $pdo->prepare(
            "UPDATE lesson_slots SET is_booked = 0, student_email_address = NULL
             WHERE slot_id = ? AND is_booked = 1 AND LOWER(TRIM(student_email_address)) = LOWER(TRIM(?))"
        );
        $update->execute([$slot_id, trim($studentEmailAddress)]);
        return $update->rowCount() > 0 ? true : false;
    }
    function getTeacherBookedSlots($teacherEmailAddress) {
    global $pdo;
    $statement = $pdo->prepare(
        "SELECT ls.slot_id, ls.teacher_email_address, ls.slot_date, ls.start_time, ls.end_time,
                ls.is_booked, ls.student_email_address,
                u.first_name AS student_first_name, u.last_name AS student_last_name,
                u.contact_number AS student_contact_number,
                t.teacher_type
         FROM lesson_slots ls
         INNER JOIN users u ON u.email_address = ls.student_email_address
         LEFT JOIN teachers t ON t.email_address = ls.teacher_email_address
         WHERE LOWER(TRIM(ls.teacher_email_address)) = LOWER(TRIM(?))
           AND ls.is_booked = 1
         ORDER BY ls.slot_date, ls.start_time"
    );
    $statement->execute([trim($teacherEmailAddress)]);
    return $statement->fetchAll(PDO::FETCH_OBJ);
}

function releaseTeacherSlot($slot_id, $teacherEmailAddress) {
    global $pdo;
    
    // First verify this slot belongs to this teacher
    $stmt = $pdo->prepare(
        "SELECT slot_id, teacher_email_address, is_booked, student_email_address 
         FROM lesson_slots 
         WHERE slot_id = ? AND LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?))"
    );
    $stmt->execute([$slot_id, $teacherEmailAddress]);
    $slot = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$slot || !$slot->is_booked) {
        return false;
    }
    
    // Release the slot
    $update = $pdo->prepare(
        "UPDATE lesson_slots 
         SET is_booked = 0, student_email_address = NULL 
         WHERE slot_id = ?"
    );
    $update->execute([$slot_id]);
    
    return $update->rowCount() > 0;
}
function deleteTeacherSlot($slot_id, $teacherEmailAddress) {
    global $pdo;
    
    $stmt = $pdo->prepare(
        "DELETE FROM lesson_slots 
         WHERE slot_id = ? 
         AND LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?)) 
         AND is_booked = 0"
    );
    $stmt->execute([$slot_id, $teacherEmailAddress]);
    
    return $stmt->rowCount() > 0;
}



// Calculate duration in hours (e.g., 1.5 for 90 minutes)
function calculateDurationHours($start_time, $end_time) {
    $start = strtotime($start_time);
    $end = strtotime($end_time);
    $diff = $end - $start;
    return $diff / 3600;
}

// Create invoice for a booking
function createInvoice($teacher_email, $student_email, $invoice_date, $total) {
    global $pdo;
    try {
        $sql = "INSERT INTO invoices (teacher_email_address, student_email_address, invoice_date, Total, status) 
                VALUES (?, ?, ?, ?, 'unpaid')";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$teacher_email, $student_email, $invoice_date, $total]);
    } catch (PDOException $e) {
        return false;
    }
}

// Get invoices for a student
function getInvoicesForStudent($student_email) {
    global $pdo;
    $sql = "SELECT i.*, t.first_name as teacher_first_name, t.last_name as teacher_last_name 
            FROM invoices i 
            JOIN users t ON i.teacher_email_address = t.email_address  /* Changed user to users */
            WHERE i.student_email_address = ? 
            ORDER BY i.invoice_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$student_email]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function getInvoicesForTeacher($teacher_email) {
    global $pdo;
    $sql = "SELECT i.*, u.first_name, u.last_name 
            FROM invoices i 
            JOIN users u ON i.student_email_address = u.email_address  /* Changed user to users */
            WHERE i.teacher_email_address = ? 
            ORDER BY i.invoice_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$teacher_email]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}



// Get teacher's bookings with invoice info (joined)
function getTeacherBookingsWithInvoices($teacher_email) {
    global $pdo;
    $sql = "SELECT 
                b.lesson_id,
                b.slot_id,
                b.student_email_address,
                b.status as booking_status,
                ls.slot_date,
                ls.start_time,
                ls.end_time,
                u.first_name as student_first_name,
                u.last_name as student_last_name,
                i.invoice_number,
                i.Total as invoice_total,
                i.status as invoice_status
            FROM bookings b
            JOIN lesson_slots ls ON b.slot_id = ls.slot_id
            JOIN users u ON b.student_email_address = u.email_address
            LEFT JOIN invoices i ON i.teacher_email_address = ls.teacher_email_address 
                                AND i.student_email_address = b.student_email_address
                                AND i.invoice_date = ls.slot_date
            WHERE LOWER(TRIM(ls.teacher_email_address)) = LOWER(TRIM(?))
            ORDER BY ls.slot_date DESC, ls.start_time DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([trim($teacher_email)]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

// Update booking status (scheduled, completed, cancelled)
function updateBookingStatus($lesson_id, $new_status) {
    global $pdo;
    $valid_statuses = ['scheduled', 'completed', 'cancelled'];
    if (!in_array($new_status, $valid_statuses)) return false;
    
    $stmt = $pdo->prepare("UPDATE bookings SET status = ? WHERE lesson_id = ?");
    return $stmt->execute([$new_status, $lesson_id]);
}

// Update invoice status (paid, unpaid, overdue)
function updateInvoiceStatus($invoice_number, $new_status) {
    global $pdo;
    $valid_statuses = ['paid', 'unpaid', 'overdue'];
    if (!in_array($new_status, $valid_statuses)) return false;
    
    $stmt = $pdo->prepare("UPDATE invoices SET status = ? WHERE invoice_number = ?");
    return $stmt->execute([$new_status, $invoice_number]);
}

// Get single booking details for verification
function getBookingById($lesson_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT b.*, ls.teacher_email_address 
        FROM bookings b
        JOIN lesson_slots ls ON b.slot_id = ls.slot_id
        WHERE b.lesson_id = ?
    ");
    $stmt->execute([$lesson_id]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

function getAllStudentsForParent($parentEmailAddress) {
    global $pdo;
    $statement = $pdo->prepare(
        "SELECT u.email_address, u.first_name, u.last_name, u.contact_number, s.student_type
         FROM student_parent sp
         JOIN users u ON LOWER(TRIM(u.email_address)) = LOWER(TRIM(sp.student_email_address))
         JOIN students s ON s.email_address = u.email_address
         WHERE LOWER(TRIM(sp.parent_email_address)) = LOWER(TRIM(?))
         ORDER BY u.last_name, u.first_name"
    );
    $statement->execute([trim($parentEmailAddress)]);
    return $statement->fetchAll(PDO::FETCH_OBJ);
}

function getRandomTeacher($requireAvailability = true) {
    global $pdo;
    
    if ($requireAvailability) {
        $sql = "SELECT DISTINCT t.email_address, t.teacher_type, u.first_name, u.last_name, u.contact_number
                FROM teachers t
                INNER JOIN users u ON u.email_address = t.email_address
                INNER JOIN lesson_slots ls ON ls.teacher_email_address = t.email_address
                WHERE ls.is_booked = 0 AND ls.slot_date >= CURDATE()
                ORDER BY RAND()
                LIMIT 1";
    } else {
        $sql = "SELECT t.email_address, t.teacher_type, u.first_name, u.last_name, u.contact_number
                FROM teachers t
                INNER JOIN users u ON u.email_address = t.email_address
                ORDER BY RAND()
                LIMIT 1";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
}

}
?>