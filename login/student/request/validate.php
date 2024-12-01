<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    include "$root/ams/login/student/conn.php";

    $response = array();

    session_start();

    if(isset($_POST["id"]) && isset($_POST["pass"]))
    {
        $sql = "SELECT stud_id, stud_ln, stud_fn, stud_m, stud_password FROM student WHERE stud_id = '{$_POST['id']}'";
        $result = $conn->query($sql);
        if($result->num_rows > 0)
        {
            $row = $result->fetch_assoc();
            $response['id'] = 1;
            //checks password
            if(password_verify($_POST["pass"], $row["stud_password"])){
                $response['password'] = 1;
                $_SESSION['stud_conn'] = 'open';
                $mname = "{$row['stud_m']} " ?? "";
                $_SESSION['full_name'] = "{$row['stud_fn']} " . "{$mname}" . "{$row['stud_ln']}";
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