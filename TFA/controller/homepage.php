<?php
session_start();
require_once "../model/dataAccess.php";


$randomTeacher = getRandomTeacher(true); 


if (!$randomTeacher) {
    $randomTeacher = getRandomTeacher(false);
}

require_once "../view/homePageView.php";
?>