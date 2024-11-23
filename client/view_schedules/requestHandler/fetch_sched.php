<?php
    $root = $_SERVER['DOCUMENT_ROOT'];
    include $root."/ams/client/global/components/conn.php";

    if(isset($_SESSION['stud_id']))
    {
        // SELECT * FROM `getuserappointments` WHERE `status` IN ('queued', 'in_progress');
        $getAppointments = $conn->prepare("SELECT * FROM getuserappointments WHERE stud_id = ? AND `status` IN ('Queued', 'Waiting') ORDER BY STATUS ASC") ;
        $getAppointments->bind_param("i", $_SESSION['stud_id']);
        $getAppointments->execute();
        $result = $getAppointments->get_result();
        
        while($rows = $result->fetch_assoc()){
            $status = '';
            if($rows['status'] === "Queued"){
                $status = "<span class='badge text-bg-primary rounded-pill' style='width: 5rem;'>Queued</span>";
            }else{
                $status = "<span class='badge text-bg-primary rounded-pill' style='width: 5rem;'>Waiting</span>";
            }
            echo "  <li class='list-group-item list-group-item-action sched-items d-flex justify-content-between align-items-start' data-appointment-id={$rows['appointment_id']}>
                        <div class='ms-2 me-auto'>
                            <div class='fw-bold'>Appointment ID: {$rows['appointment_id']}</div>
                            {$rows['appointment_date']}
                        </div>
                        $status
                    </li>
                ";
        }
        $result->close();
    }else{
        echo "Invalid Session ID";
    }

    
?>