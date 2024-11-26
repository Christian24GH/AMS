<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    include "$root/ams/login/student/conn.php";

    $response = array();

    session_start();

    if(isset($_POST["id"]) && isset($_POST["pass"]) && isset($_POST["email"]))
    {
        $id = $_POST["id"];
        $ln = $_POST["ln"];
        $fn = $_POST["fn"];
        $m = $_POST["m"];
        $email = $_POST["email"];
        $pass = $_POST["pass"];

        $stmt = $conn->prepare("SELECT stud_id FROM student WHERE stud_id = ?");
        $stmt->bind_param("s", $_POST['id']);
        $stmt->execute();
        $getId = $stmt->get_result();

        if ($getId->num_rows > 0) {
            $response['status'] = 500;
            $response['message'] = "Student ID already exists.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);

            $insertStms = $conn->prepare("INSERT INTO student(stud_id, stud_ln, stud_fn, stud_m, stud_password, stud_email) VALUES(?, ?, ?, ?, ?, ?)");
            $insertStms->bind_param("isssss", $id, $ln, $fn, $m, $hash, $email);
            $insertStms->execute();

            if ($insertStms->affected_rows > 0) {
                $response['status'] = 200;
                $response['message'] = "Student successfully registered.";
            } else {
                $response['status'] = 500;
                $response['message'] = "Unknown error occurred during insertion.";
            }
        }
    }
    header("Content-Type: application/json");
    echo json_encode($response);
    $conn->close();
?>