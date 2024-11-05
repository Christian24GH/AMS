<?php
    include 'conn.php';
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>AMS</title>
    <link rel="stylesheet" href="css/login.css">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
</head>
<body>
    <div class="full-page">
        <div class="navbar">
            <div>                
                <h1 style="color: black;">AMS</h1>            
            </div>
            <nav>
                <ul id="MenuItems">
                    <li>
                        <button class='loginbtn' 
                                onclick="document.getElementById('login-form').style.display='block'" 
                                style="width:auto; color: black; background-color: #f0f0fa;">
                            Login
                        </button>
                </ul>
            </nav>
        </div>
        <div id='login-form' class='login-page'>
            <div class="wrapper">
                <div class="form-box login">
                    <h2>Login</h2>
                    <form id="login_form">
                        <div class="input-box">
                            <input id="id_field" name="studID_field" type="text" autocomplete="false">
                            <label for="id_field">Student ID</label>
                            <i class='bx bxs-user'></i>
                        </div>
                        <div class="input-box">
                            <input id="password_field" name="password_field" type="password" autocomplete="false">
                            <label for="password_field">Password</label>
                            <i class='bx bxs-lock-alt'></i>
                        </div>
                        <button id="submit" type="submit" class="btn">Login</button>
                    </form>
                </div>
                <div class="info-text login">
                    <h2>Appointment Management System</h2>
                    <p>Please log in using your account</p>
                    <img src="icons/amsLogo.png" alt=""height="360" width="350">
                </div>
            </div>
        </div>
    </div>
    <script src="scripts/login.js"></script>
</body>
</html>