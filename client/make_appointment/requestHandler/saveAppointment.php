<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    include $root."/ams/client/global/components/conn.php";

    header('Content-Type: application/json');

    $response = array();
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data["studID"])) {
        $response['ok'] = true;
        
        $itemlist  = implode(' ', $data['itemlist']);
        $callProcedure = $conn->prepare("CALL makeAppointment(?, ?, ?, ?, ?, ?, ?, ?, 'Waiting', ?)");
        $callProcedure->bind_param("sissssdss", $data['appointmentID'], $data['studID'], $data['studFirstname'], $data['studMiddle'], $data['studLastname'], $itemlist, $data['amount'], $data['date'], $data['shift']);
        $callProcedure->execute();
        $callProcedure->close();
    } else {
        $response['ok'] = false;
        $response['error'] = "Missing Fields";
    }
    
    echo json_encode($response);


?>
