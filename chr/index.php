<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $base_url = $protocol . '://' . $host ."/ams/chr";
        define("BASE_URL", $base_url);
        
        $root = $_SERVER["DOCUMENT_ROOT"];
        
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/global/css/client.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/css/dashboard.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/node_modules/bootstrap/dist/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/global/css/fonts.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" >

    <title>Dashboard</title>
</head>
<body class="poppins-regular">
    <?php 
        include $root . '/ams/chr/global/components/session.php';
        include $root . '/ams/chr/global/components/header.php';
        include $root . '/ams/chr/global/components/left_nav.php';
    ?>
    <div class="container-fluid mt-1" >
        <div class="d-flex flex-row gap-1 ">
            <div class="card w-25" style="height: calc(90vh - (1rem * 3));">
                <div class="card-header">Scan Result</div>
                <div class="card-body">
                    <div class="user-cred">
                        <div class="student-id">Appointment ID: <span id="appointment_id"></span></div>
                        <div class="student-id">Student ID: <span id="student-id"></span></div>
                        <div class="student-name">Student Name: <span id="student-name"></span></div>
                    </div>
                    <div id="payment-items" class="payment-items">
                        Items:
                        <div id="item-list" class="vstack px-3">
                        </div>
                    </div>
                    <div class="payment-amount">Amount: <span id="payment-amount"></span></div>
                </div>
                <div class="card-footer action d-flex align-items-center justify-content-evenly">
                    <button id="rescan" class="btn btn-secondary">Rescan</button>
                    <button id="approve" class="btn btn-primary">Approve</button>
                </div>
            </div>
            <div class="container w-75 d-flex justify-content-center align-items-center border">
                <div id="reader" class="hide object-fit-cover"></div>
                <div id="start-scan-btn" class="btn btn-primary">Start Scanning</div>
                <!--<input type="file" id="qr-input-file" accept="image/*">-->
            </div>
        </div>
    </div>
    <script src="<?php echo BASE_URL;?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL;?>/node_modules/html5-qrcode/html5-qrcode.min.js"></script>
    <script src="script/reader.js"></script>
</body>
</html>