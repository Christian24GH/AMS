<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    include "$root/ams/login/conn.php";

    $response = array();

    session_start();

    if(isset($_POST["id"]) && isset($_POST["pass"]))
    {
        $sql = "SELECT stud_id, stud_password FROM student WHERE stud_id = '{$_POST['id']}'";
        $result = $conn->query($sql);
        
        if($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();

            $response['id'] = 1;
            //checks password
            if($row["stud_password"] == $_POST["pass"]){
                $response['password'] = 1;
                $_SESSION['stud_conn'] = 'open';
                $_SESSION['stud_id'] = $row['stud_id'];
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