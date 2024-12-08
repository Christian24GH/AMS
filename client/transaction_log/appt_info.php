<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        $root = $_SERVER["DOCUMENT_ROOT"];
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $base_url = $protocol . '://' . $host ."/ams/client";
        define("BASE_URL", $base_url);
        $appt_id = $_GET['id'];
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
        include "$root/ams/client/global/components/session.php";
        include "$root/ams/client/global/components/header.php";
        include "$root/ams/client/global/components/left_nav.php";
        include "$root/ams/client/global/components/loading.php";
    ?>
    <div class="container-fluid d-flex justify-content-center flex-column align-items-center margin-top-5">
        <input id="appt_id" type="hidden" value="<?php echo $appt_id;?>">
        <h4>Appointment Information</h4>
        <div id='appointment_info' class="container d-flex justify-content-start gap-2">
            <div id='m_vertical' class="card d-flex flex-grow-1">
                <div class="card-body pt-4 pb-2">
                    <div class="d-flex flex-column flex-grow-1 p-2">
                        <div class="mb-2"><div class="poppins-bold">Student ID </div><span id="studID"></span></div>
                        <div class="row p-0 mb-2">
                            <div class="col poppins-bold">Student Name</div>
                            <span id="stud_name"></span>
                        </div>
                        <div class="mb-2">
                            <div class="poppins-bold">Date and Shift </div><span id="appt_date">
                        </div>
                        <div class="">
                            <div class="poppins-bold">Amount </div><span id="amount"></span>
                        </div>
                        <hr>
                        <div class="accordion accordion-flush" style="">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" style="padding: 4px 0px; font-weight:400;" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne">
                                        <span class="poppins-bold">Items </span>
                                    </button>
                                </h2>
                                <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                                    <div class="accordion-body">
                                        <ul id="appt_items" class="list-group"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-column gap-2">
                <div class="card p-2 pt-5 d-flex align-items-center" style="min-width:15rem; min-height: 15rem;">
                    <!-- Image Placeholder -->
                    <div id="qrPlaceholder" class="placeholder-glow" style="width: 80%; aspect-ratio: 1/1;">
                        <div class="placeholder" style="width: 100%; height: 100%;"></div>
                    </div>
                    <!-- Actual Image -->
                    <img id="qr_code" class="card-img-top d-none" style="width: 80%; aspect-ratio: 1/1;"/>

                    <div class="card-body">
                        <div id="flex-end" class="d-flex justify-content-end">
                            
                        </div>
                    </div>
                </div>
                <div id='queue_status' class="card p-2">
                    <div id="notQue" class="d-flex align-items-center justify-content-center">
                        <span class="poppins-semibold me-1">Status:  </span> 
                    </div>
                </div> 
            </div>
        </div>
    </div>
    
    <script src="<?php echo BASE_URL;?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/fetch_info.js"></script>
</body>
</html>