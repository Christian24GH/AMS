<?php
    include 'global/components/conn.php';
    $qr_id = 135;
    $stud_id = 22010587;
    $shift_id = 112;
    $status = 'waiting'; // Example status, you can modify it as needed
    
    // Insert 300 appointments
    for ($i = 1; $i <= 600; $i++) {
        $sql = "INSERT INTO appointments (appointment_id, appointment_date, `status`, qr_id, stud_id, shift_id)
                VALUES ($i, NOW() + INTERVAL 5 DAY, '$status', $qr_id, $stud_id, $shift_id)";
    
        if ($conn->query($sql) === TRUE) {
            echo "Appointment $i inserted successfully.<br>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error . "<br>";
        }
    }
?>