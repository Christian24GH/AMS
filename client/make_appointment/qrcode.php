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
    <link rel="stylesheet" href="css/idx_appoint.css"/>
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
        include "$root/AMS/client/global/components/loading.php";
    ?>
    <div class="container d-flex justify-content-center py-3 margin-top-5" style="max-width: 40rem;">
        <div id='success_page' class="container text-center p-4">
            <h2 class="mb-4">QR Code Generated Successfully!</h2>
            
            <div id="qrPlaceholder" class="qr_placeholder container d-flex justify-content-center align-items-center card bg-secondary placeholder-glow" style="width:15rem; height:15rem;">
                <span class="placeholder" style='width:15rem; height:15rem;'></span>
            </div>

            <a id='btnPlaceholder' class="btn btn-primary disabled placeholder mt-4">Save Appointment</a>

            <a id='qr_container' class="qr_container container d-flex justify-content-center align-items-center card d-none" style='width:15rem; height:15rem;' download>
            </a>
            <div id="save_appointment" class="btn btn-primary mt-4 d-none">Save Appointment</div>
            
        </div>
    </div>
    
    <script src="<?php echo BASE_URL;?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src='js/submit.js'></script>
</body>
</html>
