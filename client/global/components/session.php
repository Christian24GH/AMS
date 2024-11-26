<?php
    session_start();
    if(!(isset($_SESSION['stud_conn']) && $_SESSION['stud_conn'] === 'open')){
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $base_url = $protocol . '://' . $host;
        header("Location: {$base_url}/ams/login/student/");
    }
    
?>