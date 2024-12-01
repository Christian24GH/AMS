<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $base_url = $protocol . '://' . $host ."/ams";
        define("BASE_URL", $base_url);
        
        $root = $_SERVER["DOCUMENT_ROOT"];
        
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMS</title>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/login/student/css/fonts.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/login/node_modules/bootstrap/dist/css/bootstrap.min.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" >
</head>
<body>
    <div class="container-fluid vh-100 vw-100 gap-2 d-flex align-items-center justify-content-center flex-column">
        <h5 class="text-center">Select Login Type</h5>
        <div class="d-flex align-items-center justify-content-evenly gap-2">
            <div class="card" style="width: 150px; height: 150px;">
                <div class="card-body mx-auto">
                    <a href="<?php echo BASE_URL?>/login/student/" class="text-decoration-none">Student</a>
                </div>
            </div>
            <div class="card"  style="width: 150px; height: 150px;"> 
                <div class="card-body mx-auto">
                    <a href="<?php echo BASE_URL?>/login/cashier/" class="text-decoration-none">Cashier</a>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo BASE_URL;?>/login/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>