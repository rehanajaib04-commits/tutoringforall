<?php
require_once "../model/user.php";
require_once "../model/teacher.php";
require_once "../model/dataAccess.php";

if (isset($_POST['search']) && !empty($_POST['search'])){
    $full_name = $_POST['search'];
    $results = getTeacherByName($full_name);  // Use this instead of getUserByName
} else {
   $results = getAllTeacher();
}

require_once "../view/viewTeacher_view.php";
?>