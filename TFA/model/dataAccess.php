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
    $statement = $pdo->prepare("SELECT * FROM teachers");
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

?>