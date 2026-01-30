<?php

session_start();

session_destroy();

header("Location: ../controller/sign_in.php");
exit;

?>