<?php

require_once "user.php";


$username = "root";
$password = "";
$db = "tfa1";
$servername = "localhost";

$pdo = new PDO("mysql:host=$servername;dbname=$db", $username, $password);


function addUser($first_name, $last_name, $contact_number, $email_address, $user_type, $password, $security_question, $security_answer) {
    global $pdo;
        $statement = $pdo->prepare("INSERT INTO users (email_address, first_name, last_name, contact_number, user_type, password, security_question, security_answer) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        return $statement->execute([
            $email_address, 
            $first_name, 
            $last_name, 
            $contact_number, 
            $user_type, 
            $password, 
            $security_question, 
            $security_answer
        ]);
    
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

function getUserByEmail($email_address)
{
    global $pdo;
    $statement = $pdo->prepare('SELECT * FROM users WHERE email_address = ?');
    $statement->execute([$email_address]);
    $result = $statement->fetchAll(PDO::FETCH_CLASS, 'User');
    return $result; 
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

function linkStudentParent($student_email, $parent_email) {
    global $pdo;
    $statement = $pdo->prepare("INSERT INTO student_parent (student_email_address, parent_email_address) VALUES (?, ?)");
    return $statement->execute([$student_email, $parent_email]);
}

function checkParentExists($email_address) {
    global $pdo;
    $statement = $pdo->prepare('SELECT * FROM parents WHERE email_address = ?');
    $statement->execute([$email_address]);
    $result = $statement->fetchAll(PDO::FETCH_CLASS, 'User');
    return count($result) > 0;
}
?>