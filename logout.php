<?php
    session_start();
    $log_out = filter_var($_POST['logout'], FILTER_SANITIZE_STRING);
    if(isset($log_out)){
        $_SESSION = array();
        header("Location: login.php");
        exit();
    }
?>