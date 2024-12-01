<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    include $root."/ams/client/global/components/conn.php";
    header("Content-Type: application/json");

    $result = [];
    $getSlot = $conn->query("SELECT COUNT(appointment_date) AS slot, appointment_date FROM appointments GROUP BY appointment_date");
    
    while($row = $getSlot->fetch_assoc()){
        $result[] = [
            'slot' => $row['slot'],
            'date' => $row['appointment_date']
        ];
    }

    echo json_encode($result);
    $conn->close();
?>