<?php
    session_start();
    if(!(isset($_SESSION['cashier_con']) && $_SESSION['cashier_con'] === 1)){
        header("Location: http://localhost/ams/login/login.php");
    }
?>