<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        $root = $_SERVER["DOCUMENT_ROOT"];
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $base_url = $protocol . '://' . $host ."/ams/client";
        define("BASE_URL", $base_url);
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/idx_schedules.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/global/css/media-query.css">
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/global/css/client.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/global/css/fonts.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/node_modules/bootstrap/dist/css/bootstrap.min.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" >
    <title>Appointment Management System</title>
</head>
<body class="poppins-regular">
    <?php
        include "$root/AMS/client/global/components/session.php";
        include "$root/AMS/client/global/components/header.php";
        include "$root/AMS/client/global/components/left_nav.php";
    ?>
    <div class="container my-5 d-flex justify-content-center">

        <input id="stud_id" type="hidden" value="<?php echo $_SESSION['stud_id']?>">
        <div class="w-100">
        <h2>Notifications</h2>
        <div class="list-group">
            <?php include "$root/ams/client/notification/requestHandler/disnotif.php";?>
        </div>
    </div>
    </div>
    
    <script src='<?php echo BASE_URL;?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js'></script>
    <script src='<?php echo BASE_URL;?>/global/js/main.js'></script>
</body>
</html>