<?php
    try{
        $conn = new mysqli("localhost", "root", "");
    }catch(mysqli_sql_exception){
        die("<div class='conn_err'> Connection Failed </div>");
    }
?>