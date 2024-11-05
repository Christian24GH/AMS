<?php
    session_start();
    if(!(isset($_SESSION['open']) && $_SESSION['open'] === 1)){
        header("Location: ../login/login.php");
    }
?>