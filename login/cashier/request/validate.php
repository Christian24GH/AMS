<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    include "$root/ams/login/cashier/conn.php";

    $response = array();

    session_start();

    if(isset($_POST["id"]) && isset($_POST["pass"]))
    {
        $sql = "SELECT cashier_id, cashier_name,password FROM cashiers WHERE cashier_id = '{$_POST['id']}'";
        $result = $conn->query($sql);
        
        if($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();

            $response['id'] = 1;
            //checks password
            if($row["password"] == $_POST["pass"]){
                $response['password'] = 1;
                $_SESSION['cashier_con'] = 1;
                $_SESSION['cashier_name'] = $row['cashier_name'];
                $_SESSION['cashier_id'] = $row['cashier_id'];
            }
            else{
                $response['password'] = 0;
            }
        }
        else{
            $response['id'] = 0;
            $response['password'] = 0;
        }
    }
    
    echo json_encode($response);
    $conn->close();
?>