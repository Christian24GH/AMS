<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    include "$root/ams/chr/conn.php";

    $transactions = $conn->query("SELECT * FROM transactions WHERE cashier_id = {$_SESSION['cashier_id']} ORDER BY date desc");
    while($rows = $transactions->fetch_assoc()){
        echo "  <li class='list-group-item sched-items d-flex justify-content-between align-items-start' data-appointment-id={$rows['appointment_id']}>
                    <div class='ms-2 me-auto'>
                        <div class='fw-bold'>Appointment ID: {$rows['appointment_id']}</div>
                        Student ID: {$rows['stud_id']}
                    </div>
                    {$rows['date']}
                </li>
            ";
    }
    $transactions->close();
?>