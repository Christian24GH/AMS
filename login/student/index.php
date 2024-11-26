<?php
    include 'conn.php';
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $base_url = $protocol . '://' . $host ."/ams/login";
        define("BASE_URL", $base_url);
        
        $root = $_SERVER["DOCUMENT_ROOT"];
    ?>
    <meta charset="utf-8">
    <title>AMS</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/student/css/fonts.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" >
</head>
<body class="poppins-regular">
    <div class="container-fluid my-5" style="">
        <div class="w-100">
            <div class="card w-100 h-100 mx-auto border-1" style="min-width: 300px; max-width:calc(0.5 * 768px); min-height: 20rem;">
                <div class="card-image bg-dark d-flex justify-content-center" style="max-height: 10rem; width: 100%;">
                    <img class="object-fit-cover" src="<?php echo BASE_URL;?>/icons/ams.png" alt="" style="max-height: 100%; max-width: 15rem;">
                </div>
                <h5 class="card-header mt-2 border-0 bg-transparent">Login</h5>
                <div class="card-body px-3 py-3 d-flex justify-content-center flex-column">
                    <form class="form" id="login_form">
                        <div class="invalid-feedback">
                                Invalid Username or password
                            </div>
                        <div class="input-group has-validation mb-3">
                            <div class="form-floating ">
                                <input id="id_field" class="form-control" name="studID_field" type="text" placeholder="ID" autocomplete="false" required />
                                <label for="id_field">Student ID</label>
                            </div>
                        </div>
                        <div class="input-group has-validation mb-4">
                            <div class="form-floating">
                                <input id="password_field" class="form-control" name="password_field" type="password" placeholder="Password" autocomplete="false" required/>
                                <label for="password_field">Password</label>
                            </div>
                        </div>
                        <div class="container d-flex justify-content-center" >
                            <button class="btn btn-primary p-2" type="submit" form="login_form" style="width: 10rem;">Sign in</button>
                        </div>
                    </form>
                    <hr class="border border-1 opacity-75">
                    <div class="d-flex align-items-center justify-content-center flex-column">
                        <a href="register.php" class="text-decoration-none">Create account</a>
                        <a href="<?php echo BASE_URL;?>/cashier/index.php" class="text-decoration-none">Login as cashier</a>
                    </div>
                </div>
                <div class="card-footer border-0 m-2 bg-info-subtle p-1 text-info-emphasis" style="font-size: .5em;">
                    <p class="card-text text-center"><b>Appointment Management System (2024)</b> is committed to protecting your information and credentials. All data is handled securely to ensure your privacy and security.</p>
                </div>
            </div>
        </div>
    </div>
<!-- 
    
    
-->
    <script src="<?php echo BASE_URL;?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts/login.js"></script>
</body>
</html>