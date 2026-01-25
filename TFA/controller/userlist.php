<?php
require_once "../model/user.php";
require_once "../model/dataAccess.php";

if (isset($_POST['search']) && ($_POST['search'])){
    $email_address = $_POST['search'];
    $results = getUserByEmail($email_address);
}else{
   $results = getAllUser();
}



require_once "../view/viewUser_view.php"
?>