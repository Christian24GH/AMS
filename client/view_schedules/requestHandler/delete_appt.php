<?php
    header('Content-Type: application/json');
    $root = $_SERVER['DOCUMENT_ROOT'];
    include $root . "/ams/client/global/components/conn.php";

    $response = [];

    $input = json_decode(file_get_contents("php://input"), true);
    $response = []; // Initialize response array
    if (isset($input['appointment_id'])) {
        $appt_id = $input['appointment_id'];
        
        // Prepare and execute the deletion
        $stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE appointment_id = ?");
        $stmt->bind_param("i", $appt_id);
        $result = $stmt->execute();

        // Check the result of the deletion
        if ($result) {
            $response['status'] = "ok";
        } else {
            $response['status'] = "error";
        }

        $stmt->close();
    } else {
        $response['status'] = "error"; // Handle missing appointment_id
    }
    
    $conn->close();
    echo json_encode($response);
?>