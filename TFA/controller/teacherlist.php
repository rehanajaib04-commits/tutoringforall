<?php
require_once "../model/user.php";
require_once "../model/dataAccess.php";

if (isset($_POST['search']) && ($_POST['search'])){
    $email_address = $_POST['search'];
    $results = getUserByEmail($email_address);
}else{
   $results = getAllTeacher();
}



require_once "../view/viewTeacher_view.php"
?>