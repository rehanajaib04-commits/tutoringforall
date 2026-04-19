<?php

require_once "user.php";
require_once "bookings.php";

$username = "root";
$password = "";
$db = "tutoringforall";
$servername = "localhost";

$pdo = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function addUser($first_name, $last_name, $contact_number, $email_address, $user_type, $password, $security_question, $security_answer, $date_of_birth = null, $gender = null, $ethnicity = null) {
    global $pdo;
    $statement = $pdo->prepare("INSERT INTO users (email_address, first_name, last_name, contact_number, user_type, password, security_question, security_answer, date_of_birth, gender, ethnicity) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    return $statement->execute([
        $email_address, $first_name, $last_name, $contact_number,
        $user_type, $password, $security_question, $security_answer,
        $date_of_birth, $gender, $ethnicity
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
    $statement = $pdo->prepare("INSERT INTO address (post_code, address_line1, addressline2, town, county) VALUES (?, ?, ?, ?, ?)");
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
        "SELECT t.email_address, t.teacher_type, t.hourly_rate, t.bio, t.experience, t.rating, t.total_reviews,
                u.first_name, u.last_name, u.contact_number
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
        "SELECT t.email_address, t.teacher_type, t.hourly_rate, t.bio, t.experience, t.rating, t.total_reviews,
                u.first_name, u.last_name, u.contact_number
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
        "SELECT t.email_address, t.teacher_type, t.hourly_rate, t.bio, t.experience, t.rating, t.total_reviews,
                u.first_name, u.last_name, u.contact_number
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
                t.teacher_type,
                i.invoice_number, i.Total AS invoice_total, i.status AS invoice_status
         FROM lesson_slots ls
         INNER JOIN users u ON u.email_address = ls.teacher_email_address
         LEFT JOIN teachers t ON t.email_address = ls.teacher_email_address
         LEFT JOIN invoices i ON i.teacher_email_address = ls.teacher_email_address
                             AND i.student_email_address = ls.student_email_address
                             AND i.invoice_date = ls.slot_date
         WHERE LOWER(TRIM(ls.student_email_address)) = LOWER(TRIM(?))
           AND ls.is_booked = 1
         ORDER BY ls.slot_date, ls.start_time"
    );
    $statement->execute([trim($studentEmailAddress)]);
    return $statement->fetchAll(PDO::FETCH_OBJ);
}

// --- Profile update functions ---


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
        
        // First verify the booking exists and belongs to this student
        $stmt = $pdo->prepare(
            "SELECT slot_id, is_booked, student_email_address FROM lesson_slots WHERE slot_id = ?"
        );
        $stmt->execute([$slot_id]);
        $slot = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$slot || !$slot->is_booked) return 'not_found';
        if (strtolower(trim($slot->student_email_address)) !== strtolower(trim($studentEmailAddress))) return 'not_owner';
        
        $pdo->beginTransaction();
        
        try {
            // Step 1: Delete from bookings table
            $deleteBooking = $pdo->prepare(
                "DELETE FROM bookings 
                 WHERE slot_id = ? 
                 AND LOWER(TRIM(student_email_address)) = LOWER(TRIM(?))"
            );
            $deleteBooking->execute([$slot_id, trim($studentEmailAddress)]);
            
            // Step 2: Release the lesson slot
            $update = $pdo->prepare(
                "UPDATE lesson_slots 
                 SET is_booked = 0, student_email_address = NULL
                 WHERE slot_id = ? 
                 AND is_booked = 1 
                 AND LOWER(TRIM(student_email_address)) = LOWER(TRIM(?))"
            );
            $update->execute([$slot_id, trim($studentEmailAddress)]);
            
            if ($update->rowCount() === 0) {
                $pdo->rollBack();
                return false;
            }
            
            $pdo->commit();
            return true;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            return false;
        }
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

    try {
        $sql = "
            SELECT 
                u.email_address,
                u.first_name,
                u.last_name,
                u.contact_number,
                u.date_of_birth,
                u.gender,
                u.ethnicity,
                s.student_type
            FROM student_parent sp
            INNER JOIN users u 
                ON LOWER(TRIM(u.email_address)) = LOWER(TRIM(sp.student_email_address))
            INNER JOIN students s 
                ON LOWER(TRIM(s.email_address)) = LOWER(TRIM(u.email_address))
            WHERE LOWER(TRIM(sp.parent_email_address)) = LOWER(TRIM(:parent_email))
            ORDER BY u.last_name ASC, u.first_name ASC
        ";

        $statement = $pdo->prepare($sql);
        $statement->bindValue(':parent_email', $parentEmailAddress);
        $statement->execute();

        $students = $statement->fetchAll(PDO::FETCH_OBJ);

        return $students ?: [];

    } catch (PDOException $e) {
        error_log("getAllStudentsForParent error: " . $e->getMessage());
        return [];
    }
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



}function updateTeacherProfile($email_address, $bio, $experience, $teacher_type, $hourly_rate) {
    global $pdo;
    $stmt = $pdo->prepare("
        UPDATE teachers 
        SET bio = ?, experience = ?, teacher_type = ?, hourly_rate = ?
        WHERE email_address = ?
    ");
    return $stmt->execute([$bio, $experience, $teacher_type, $hourly_rate, $email_address]);
}
function getTeacherSubjectsWithYearGroups($teacher_email) {
    global $pdo;
    $stmt = $pdo->prepare(
        "SELECT DISTINCT s.subject_id, s.subject_name, s.key_stage, sy.year
         FROM teachers_subjects ts
         JOIN subjects s ON ts.subject_id = s.subject_id
         LEFT JOIN syllabus sy ON s.subject_name = sy.subject AND s.key_stage = sy.key_stage
         WHERE ts.teacher_email_address = ?
         ORDER BY s.key_stage, sy.year, s.subject_name"
    );
    $stmt->execute([$teacher_email]);
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function getAllSubjects() {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT s.subject_id, s.subject_name, s.key_stage, sy.year 
        FROM subjects s
        LEFT JOIN syllabus sy ON s.subject_name = sy.subject AND s.key_stage = sy.key_stage
        ORDER BY s.key_stage, s.subject_name, sy.year
    ");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function addTeacherSubject($teacher_email, $subject_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        INSERT INTO teachers_subjects (teacher_email_address, subject_id) 
        VALUES (?, ?)
    ");
    return $stmt->execute([$teacher_email, $subject_id]);
}

function removeTeacherSubject($teacher_email, $subject_id) {
    global $pdo;
    $stmt = $pdo->prepare("
        DELETE FROM teachers_subjects 
        WHERE teacher_email_address = ? AND subject_id = ?
    ");
    return $stmt->execute([$teacher_email, $subject_id]);
}
function hasStudentBookedTeacher($studentEmailAddress, $teacherEmailAddress) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM bookings b
        INNER JOIN lesson_slots ls ON b.slot_id = ls.slot_id
        WHERE LOWER(TRIM(b.student_email_address)) = LOWER(TRIM(?))
          AND LOWER(TRIM(ls.teacher_email_address)) = LOWER(TRIM(?))
          AND b.status <> 'cancelled'
    ");
    $stmt->execute([trim($studentEmailAddress), trim($teacherEmailAddress)]);

    return (int)$stmt->fetchColumn() > 0;
}

function getEligibleStudentsForParentReview($parentEmailAddress, $teacherEmailAddress) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT DISTINCT
            u.email_address,
            u.first_name,
            u.last_name
        FROM student_parent sp
        INNER JOIN users u
            ON LOWER(TRIM(u.email_address)) = LOWER(TRIM(sp.student_email_address))
        INNER JOIN bookings b
            ON LOWER(TRIM(b.student_email_address)) = LOWER(TRIM(sp.student_email_address))
        INNER JOIN lesson_slots ls
            ON ls.slot_id = b.slot_id
        WHERE LOWER(TRIM(sp.parent_email_address)) = LOWER(TRIM(?))
          AND LOWER(TRIM(ls.teacher_email_address)) = LOWER(TRIM(?))
          AND b.status <> 'cancelled'
        ORDER BY u.first_name, u.last_name
    ");
    $stmt->execute([trim($parentEmailAddress), trim($teacherEmailAddress)]);

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}

function getTeacherReviewByReviewer($teacherEmailAddress, $reviewerEmailAddress, $studentEmailAddress) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT *
        FROM teacher_reviews
        WHERE LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?))
          AND LOWER(TRIM(reviewer_email_address)) = LOWER(TRIM(?))
          AND LOWER(TRIM(student_email_address)) = LOWER(TRIM(?))
        LIMIT 1
    ");
    $stmt->execute([
        trim($teacherEmailAddress),
        trim($reviewerEmailAddress),
        trim($studentEmailAddress)
    ]);

    return $stmt->fetch(PDO::FETCH_OBJ);
}

function updateTeacherReviewSummary($teacherEmailAddress) {
    global $pdo;

    $summaryStmt = $pdo->prepare("
        SELECT 
            COUNT(*) AS total_reviews,
            ROUND(AVG(rating), 1) AS average_rating
        FROM teacher_reviews
        WHERE LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?))
    ");
    $summaryStmt->execute([trim($teacherEmailAddress)]);
    $summary = $summaryStmt->fetch(PDO::FETCH_OBJ);

    $totalReviews = (int)($summary->total_reviews ?? 0);
    $averageRating = $totalReviews > 0 ? (float)$summary->average_rating : 0;

    $updateStmt = $pdo->prepare("
        UPDATE teachers
        SET rating = ?, total_reviews = ?
        WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?))
    ");
    $updateStmt->execute([$averageRating, $totalReviews, trim($teacherEmailAddress)]);
}

function saveTeacherReview($teacherEmailAddress, $reviewerEmailAddress, $reviewerType, $studentEmailAddress, $rating, $reviewText) {
    global $pdo;

    $stmt = $pdo->prepare("
        INSERT INTO teacher_reviews (
            teacher_email_address,
            reviewer_email_address,
            reviewer_type,
            student_email_address,
            rating,
            review_text
        ) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            reviewer_type = VALUES(reviewer_type),
            rating = VALUES(rating),
            review_text = VALUES(review_text),
            updated_at = CURRENT_TIMESTAMP
    ");

    $result = $stmt->execute([
        trim($teacherEmailAddress),
        trim($reviewerEmailAddress),
        trim($reviewerType),
        trim($studentEmailAddress),
        (int)$rating,
        trim($reviewText)
    ]);

    updateTeacherReviewSummary($teacherEmailAddress);

    return $result;
}

function getTeacherReviews($teacherEmailAddress) {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT
            tr.*,
            reviewer.first_name AS reviewer_first_name,
            reviewer.last_name AS reviewer_last_name,
            student.first_name AS student_first_name,
            student.last_name AS student_last_name
        FROM teacher_reviews tr
        INNER JOIN users reviewer
            ON LOWER(TRIM(reviewer.email_address)) = LOWER(TRIM(tr.reviewer_email_address))
        LEFT JOIN users student
            ON LOWER(TRIM(student.email_address)) = LOWER(TRIM(tr.student_email_address))
        WHERE LOWER(TRIM(tr.teacher_email_address)) = LOWER(TRIM(?))
        ORDER BY tr.updated_at DESC, tr.review_id DESC
    ");
    $stmt->execute([trim($teacherEmailAddress)]);

    return $stmt->fetchAll(PDO::FETCH_OBJ);
}
function getAllUser() {
    global $pdo;
    $statement = $pdo->prepare("SELECT * FROM users ORDER BY last_name, first_name");
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_CLASS, 'User');
}
function tableExists($tableName) {
    global $pdo, $db;

    static $tableCache = [];

    if (isset($tableCache[$tableName])) {
        return $tableCache[$tableName];
    }

    $statement = $pdo->prepare("
        SELECT COUNT(*)
        FROM information_schema.tables
        WHERE table_schema = ? AND table_name = ?
    ");
    $statement->execute([$db, $tableName]);

    $tableCache[$tableName] = (bool) $statement->fetchColumn();
    return $tableCache[$tableName];
}

function executeIfTableExists($tableName, $sql, array $params = []) {
    global $pdo;

    if (!tableExists($tableName)) {
        return 0;
    }

    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    return $statement->rowCount();
}

function deleteUserByEmail($email_address) {
    global $pdo;

    $email_address = trim($email_address);
    if ($email_address === '') {
        return false;
    }

    $check = $pdo->prepare("
        SELECT email_address
        FROM users
        WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?))
        LIMIT 1
    ");
    $check->execute([$email_address]);

    if (!$check->fetchColumn()) {
        return false;
    }

    try {
        $pdo->beginTransaction();

        executeIfTableExists('teacher_reviews', "
            DELETE FROM teacher_reviews
            WHERE LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?))
               OR LOWER(TRIM(reviewer_email_address)) = LOWER(TRIM(?))
               OR LOWER(TRIM(student_email_address)) = LOWER(TRIM(?))
        ", [$email_address, $email_address, $email_address]);

        executeIfTableExists('payments', "
            DELETE FROM payments
            WHERE lesson_id IN (
                SELECT lesson_id
                FROM bookings
                WHERE LOWER(TRIM(student_email_address)) = LOWER(TRIM(?))
            )
        ", [$email_address]);

        executeIfTableExists('payments', "
            DELETE FROM payments
            WHERE lesson_id IN (
                SELECT b.lesson_id
                FROM bookings b
                INNER JOIN lesson_slots ls ON ls.slot_id = b.slot_id
                WHERE LOWER(TRIM(ls.teacher_email_address)) = LOWER(TRIM(?))
            )
        ", [$email_address]);

        executeIfTableExists('bookings', "
            DELETE FROM bookings
            WHERE LOWER(TRIM(student_email_address)) = LOWER(TRIM(?))
               OR slot_id IN (
                    SELECT slot_id
                    FROM lesson_slots
                    WHERE LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?))
               )
        ", [$email_address, $email_address]);

        executeIfTableExists('lesson_slots', "
            UPDATE lesson_slots
            SET is_booked = 0, student_email_address = NULL
            WHERE LOWER(TRIM(student_email_address)) = LOWER(TRIM(?))
        ", [$email_address]);

        executeIfTableExists('student_parent', "
            DELETE FROM student_parent
            WHERE LOWER(TRIM(student_email_address)) = LOWER(TRIM(?))
               OR LOWER(TRIM(parent_email_address)) = LOWER(TRIM(?))
        ", [$email_address, $email_address]);

        executeIfTableExists('teachers_subjects', "
            DELETE FROM teachers_subjects
            WHERE LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?))
        ", [$email_address]);

        executeIfTableExists('lesson_slots', "
            DELETE FROM lesson_slots
            WHERE LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?))
        ", [$email_address]);

        executeIfTableExists('user_address', "
            DELETE FROM user_address
            WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?))
        ", [$email_address]);

        executeIfTableExists('parents', "
            DELETE FROM parents
            WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?))
        ", [$email_address]);

        executeIfTableExists('students', "
            DELETE FROM students
            WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?))
        ", [$email_address]);

        executeIfTableExists('teachers', "
            DELETE FROM teachers
            WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?))
        ", [$email_address]);

        $statement = $pdo->prepare("
            DELETE FROM users
            WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?))
        ");
        $statement->execute([$email_address]);

        if ($statement->rowCount() === 0) {
            $pdo->rollBack();
            return false;
        }

        $pdo->commit();
        return true;
    } catch (Throwable $exception) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $exception;
    }
}
function updateUserProfile($email_address, $first_name, $last_name, $contact_number, $new_password = null, $date_of_birth = null, $gender = null, $ethnicity = null) {
    global $pdo;
    $fields = [];
    $params = [];

    $fields[] = "first_name = ?";
    $params[] = $first_name;
    $fields[] = "last_name = ?";
    $params[] = $last_name;
    $fields[] = "contact_number = ?";
    $params[] = $contact_number;

    if ($new_password !== null) {
        $fields[] = "password = ?";
        $params[] = $new_password;
    }
    if ($date_of_birth !== null) {
        $fields[] = "date_of_birth = ?";
        $params[] = $date_of_birth ?: null;
    }
    if ($gender !== null) {
        $fields[] = "gender = ?";
        $params[] = $gender ?: null;
    }
    if ($ethnicity !== null) {
        $fields[] = "ethnicity = ?";
        $params[] = $ethnicity ?: null;
    }

    $params[] = $email_address;
    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE email_address = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
}
function getUserAddress($email_address) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT a.post_code, a.address_line1, a.addressline2, a.town, a.county
        FROM address a
        JOIN user_address ua ON a.address_line1 = ua.address_line1 AND a.post_code = ua.post_code
        WHERE ua.email_address = ?
        LIMIT 1
    ");
    $stmt->execute([$email_address]);
    return $stmt->fetch(PDO::FETCH_OBJ);
}

function updateUserAddress($email_address, $postcode, $addressline1, $addressline2, $town, $county) {
    global $pdo;
    try {
        $pdo->beginTransaction();
        
        // Check if address exists
        $stmt = $pdo->prepare("SELECT * FROM address WHERE post_code = ? AND address_line1 = ?");
        $stmt->execute([$postcode, $addressline1]);
        if (!$stmt->fetch()) {
            // Insert new address
            $stmt = $pdo->prepare("INSERT INTO address (post_code, address_line1, addressline2, town, county) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$postcode, $addressline1, $addressline2, $town, $county]);
        } else {
            // Update existing
            $stmt = $pdo->prepare("UPDATE address SET addressline2 = ?, town = ?, county = ? WHERE post_code = ? AND address_line1 = ?");
            $stmt->execute([$addressline2, $town, $county, $postcode, $addressline1]);
        }
        
        // Remove old link and create new one
        $stmt = $pdo->prepare("DELETE FROM user_address WHERE email_address = ?");
        $stmt->execute([$email_address]);
        $stmt = $pdo->prepare("INSERT INTO user_address (email_address, address_line1, post_code) VALUES (?, ?, ?)");
        $stmt->execute([$email_address, $addressline1, $postcode]);
        
        $pdo->commit();
        return true;
    } catch (Exception $e) {
        $pdo->rollBack();
        return false;
    }
}
function getStudentTypeByEmail($email_address) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT student_type FROM students WHERE LOWER(TRIM(email_address)) = LOWER(TRIM(?)) LIMIT 1");
    $stmt->execute([trim($email_address)]);
    $result = $stmt->fetchColumn();
    return $result !== false ? (string)$result : '';
}


function getParentForStudent($student_email) {
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT parent_email_address 
        FROM student_parent 
        WHERE LOWER(TRIM(student_email_address)) = LOWER(TRIM(?)) 
        LIMIT 1
    ");
    $stmt->execute([trim($student_email)]);
    $result = $stmt->fetchColumn();
    return $result !== false ? trim($result) : '';
}

function getParentFeedback($teacher_email, $parent_email) {
    global $pdo;
    if (empty($teacher_email) || empty($parent_email)) return '';
    $stmt = $pdo->prepare("
        SELECT feedback 
        FROM feedback_parents 
        WHERE LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?)) 
          AND LOWER(TRIM(parent_email_address)) = LOWER(TRIM(?)) 
        ORDER BY fb_date DESC 
        LIMIT 1
    ");
    $stmt->execute([trim($teacher_email), trim($parent_email)]);
    $result = $stmt->fetchColumn();
    return $result !== false ? (string)$result : '';
}

function getStudentFeedback($teacher_email, $student_email) {
    global $pdo;
    if (empty($teacher_email) || empty($student_email)) return '';
    $stmt = $pdo->prepare("
        SELECT feedback 
        FROM feedback_students 
        WHERE LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?)) 
          AND LOWER(TRIM(student_email_address)) = LOWER(TRIM(?)) 
        ORDER BY fb_date DESC 
        LIMIT 1
    ");
    $stmt->execute([trim($teacher_email), trim($student_email)]);
    $result = $stmt->fetchColumn();
    return $result !== false ? (string)$result : '';
}

function submitParentFeedback($teacher_email, $parent_email, $feedback_text) {
    global $pdo;
    if (empty($teacher_email) || empty($parent_email)) return false;

    $pdo->beginTransaction();
    try {
        $del = $pdo->prepare("
            DELETE FROM feedback_parents 
            WHERE LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?)) 
              AND LOWER(TRIM(parent_email_address)) = LOWER(TRIM(?))
        ");
        $del->execute([trim($teacher_email), trim($parent_email)]);

        $ins = $pdo->prepare("
            INSERT INTO feedback_parents (teacher_email_address, parent_email_address, feedback, fb_date) 
            VALUES (?, ?, ?, CURRENT_TIMESTAMP)
        ");
        $ins->execute([trim($teacher_email), trim($parent_email), $feedback_text]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return false;
    }
}

function submitStudentFeedback($teacher_email, $student_email, $feedback_text) {
    global $pdo;
    if (empty($teacher_email) || empty($student_email)) return false;

    $pdo->beginTransaction();
    try {
        $del = $pdo->prepare("
            DELETE FROM feedback_students 
            WHERE LOWER(TRIM(teacher_email_address)) = LOWER(TRIM(?)) 
              AND LOWER(TRIM(student_email_address)) = LOWER(TRIM(?))
        ");
        $del->execute([trim($teacher_email), trim($student_email)]);

        $ins = $pdo->prepare("
            INSERT INTO feedback_students (teacher_email_address, student_email_address, feedback, fb_date) 
            VALUES (?, ?, ?, CURRENT_TIMESTAMP)
        ");
        $ins->execute([trim($teacher_email), trim($student_email), $feedback_text]);

        $pdo->commit();
        return true;
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        return false;
    }
}

function getAllCurrentBookings() {
    global $pdo;
    $statement = $pdo->prepare(
        "SELECT 
            ls.slot_id, 
            ls.teacher_email_address, 
            ls.slot_date, 
            ls.start_time, 
            ls.end_time,
            ls.is_booked, 
            ls.student_email_address,
            tu.first_name AS teacher_first_name, 
            tu.last_name AS teacher_last_name,
            t.teacher_type,
            su.first_name AS student_first_name, 
            su.last_name AS student_last_name
         FROM lesson_slots ls
         INNER JOIN users tu ON tu.email_address = ls.teacher_email_address
         LEFT JOIN teachers t ON t.email_address = ls.teacher_email_address
         LEFT JOIN users su ON su.email_address = ls.student_email_address
         WHERE ls.is_booked = 1
         ORDER BY ls.slot_date, ls.start_time"
    );
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_OBJ);
}