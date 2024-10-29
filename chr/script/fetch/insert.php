<?php
    if(isset($_POST["items"])){
        include '../../conn.php';

        $response = array();
        
        $stdID = $_POST["studID"];
        $stdLN = $_POST["studLN"];
        $stdFN = $_POST["studFN"];
        $stdM = $_POST["studM"];
        $amount = $_POST["amount"];
        $items = $_POST["items"];

        //$insert = "INSERT INTO student(std_id, std_ln, std_fn, std_m) VALUES ('$stdID', '$stdLN', '$stdFN', '$stdM')";
        $insert = "INSERT INTO  VALUES ()";
        if($conn->query($insert)){
            $response["status"] = 200;
            $conn->close();
        }else{
            $response["status"] = $conn->error;
            $conn->close();
        }

        header("Content-Type: application/json");
        echo json_encode($response);
    }

?>