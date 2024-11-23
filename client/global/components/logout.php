<?php
    session_start();
    session_destroy();

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $base_url = $protocol . '://' . $host; // Adjust this if your app is in a subdirectory
    header("Location: {$base_url}/ams/login_student/login.php");
?>