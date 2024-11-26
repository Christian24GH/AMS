<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    include "$root/ams/chr/conn.php";
    header("Content-Type: application/json");

    $response = [];
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $conn->prepare("SELECT stud_ln, stud_fn, stud_m from student where stud_id = ?");
    $stmt->bind_param("i", $data['studID']);
    $stmt->execute();
    $result = $stmt->get_result();
    while($rows = $result->fetch_assoc()){
        $response['stud_ln'] = $rows['stud_ln'];
        $response['stud_fn'] = $rows['stud_fn'];
        $response['stud_m'] = $rows['stud_m'];
    }
    echo json_encode($response);
    $conn->close();
?>