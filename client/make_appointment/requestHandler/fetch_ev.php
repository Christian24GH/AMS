<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    include $root."/ams/client/global/components/conn.php";
    header("Content-Type: application/json");

    $data = json_decode(file_get_contents("php://input"), true);
    if(isset($data['studID'])){
        $fetchev = $conn->prepare("SELECT appointment_id, appointment_date FROM appointments WHERE stud_id = ?");
        $fetchev->bind_param("i", $data['studID']);
        $fetchev->execute();
        $result = $fetchev->get_result();
    
        $events = [];
        while($row = $result->fetch_assoc()){
            $events[] = [
                'id' => $row['appointment_id'],
                'title' => 'Appointment ' . $row['appointment_id'],        
                'start' => $row['appointment_date']
            ];
        }
    }
    echo json_encode($events);
    $conn->close();
?>