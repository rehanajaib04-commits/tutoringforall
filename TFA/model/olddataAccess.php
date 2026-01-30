<?php 
 require_once "user.php";
require_once "teacher.php";
$username = "root";
$password = "";
$db = "tfa";
$servername = "localhost";

$pdo = new PDO("mysql:host=$servername;dbname=$db", $username, $password);

function getAllUser(){
    global $pdo;
    $statement = $pdo->prepare("SELECT * FROM users");
    $statement -> execute();
    $result = $statement->fetchAll(PDO::FETCH_CLASS,"User");
    return $result;
}

function getAllTeacher(){
    global $pdo;
    $statement = $pdo->prepare("SELECT t.*, u.first_name, u.last_name 
                                FROM teachers t 
                                JOIN users u ON t.teacher_id = u.user_id");
    $statement -> execute();
    $result = $statement->fetchAll(PDO::FETCH_CLASS,"Teacher");
    return $result;
}
function getUserByEmail($email_address)
{
    global $pdo;
    $statment = $pdo -> prepare('SELECT * FROM users WHERE email_address = ?');
    $statment-> execute([$email_address]);
    $result = $statment->fetchAll(PDO::FETCH_CLASS, 'User');
    return $result; 
}

function loginUser($email_address, $password){
    global $pdo;
    $statement = $pdo->prepare('SELECT * FROM users WHERE email_address = ? AND password = ?');
    $statement->execute([
        $email_address,
        $password
    ]);
   $result = $statement->fetchAll(PDO::FETCH_CLASS, 'User');
   return $result;
}


function addUser($first_name, $last_name, $contact_number, $email_address, $user_type, $password, $security_question, $security_answer) {
    global $pdo;
    $statement = $pdo->prepare("INSERT INTO users (first_name, last_name, contact_number, email_address, user_type, password, security_question, security_answer) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    return $statement->execute([
        $first_name, $last_name, $contact_number, $email_address, $user_type, $password, $security_question, $security_answer
    ]);
}



// Add to model/dataAccess.php

function getTeacherDetails($teacher_id) {
    global $pdo;
    $statement = $pdo->prepare("SELECT u.first_name, u.last_name, t.* 
                                FROM users u 
                                JOIN teachers t ON u.user_id = t.teacher_id 
                                WHERE t.teacher_id = ?");
    $statement->execute([$teacher_id]);
    return $statement->fetch(PDO::FETCH_OBJ);
}

function updateTeacherProfile($teacher_id, $bio, $qualifications, $teacher_type, $email_address, $contact_number, $photo_path) {
    global $pdo;
    $statement = $pdo->prepare("UPDATE teachers 
                                SET bio = ?, 
                                    qualifications = ?, 
                                    teacher_type = ?, 
                                    email_address = ?, 
                                    contact_number = ?, 
                                    photo_path = ? 
                                WHERE teacher_id = ?");
    return $statement->execute([
        $bio, 
        $qualifications, 
        $teacher_type, 
        $email_address, 
        $contact_number, 
        $photo_path, 
        $teacher_id
    ]);
}

function getTeacherByName($search_name) {
    global $pdo;
    $search = "%$search_name%";
    $statement = $pdo->prepare("SELECT t.*, u.first_name, u.last_name 
                                FROM teachers t 
                                JOIN users u ON t.teacher_id = u.user_id 
                                WHERE u.first_name LIKE ? 
                                OR u.last_name LIKE ? 
                                OR CONCAT(u.first_name, ' ', u.last_name) LIKE ?");
    $statement->execute([$search, $search, $search]);
    return $statement->fetchAll(PDO::FETCH_CLASS, "Teacher");
}


function getTeacherAvailability($teacher_id) {
    global $pdo;
    $statement = $pdo->prepare("
        SELECT booking_id, date, start_time 
        FROM bookings 
        WHERE teacher_id = ? 
        AND status = 'available' 
        AND date >= CURDATE()
        ORDER BY date ASC, start_time ASC
    ");
    $statement->execute([$teacher_id]);
  
    return $statement->fetchAll(PDO::FETCH_CLASS, "Booking");
}

function bookLesson($booking_id, $student_id) {
    global $pdo;
    $statement = $pdo->prepare("UPDATE bookings 
                                SET student_id = ?, status = 'booked' 
                                WHERE booking_id = ? AND status = 'available'");
    return $statement->execute([$student_id, $booking_id]);
}
?>