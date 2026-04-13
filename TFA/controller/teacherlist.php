<?php
session_start();
require_once "../model/user.php";
require_once "../model/teacher.php";
require_once "../model/dataAccess.php";

if (isset($_POST['search']) && !empty(trim($_POST['search']))) {
    $full_name = trim($_POST['search']);
    $results = getTeacherByName($full_name);
} else {
    $results = getAllTeacher();
}

require_once "../view/teacherListView.php";
?>