<?php
    try{
        $conn = new mysqli("localhost", "root", "");
        $conn->select_db("appointment_management_system");
    }catch(mysqli_sql_exception){
        die("<div class='conn_err'> Connection Failed </div>");
    }
?>