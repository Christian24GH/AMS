<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/src/Exception.php';
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';

    $root = $_SERVER['DOCUMENT_ROOT'];
    include $root."/ams/client/global/components/conn.php";

    $stud_id = $_SESSION['stud_id'];
    $stmt = $conn->prepare("SELECT * FROM getnotification WHERE stud_id = ?");
    $stmt->bind_param("i", $stud_id);  
    $stmt->execute();

    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()){
        $appointment_date = $row['appointment_date'];
        $formatted_date = date('m/d/y', strtotime($appointment_date));

        $time = new DateTime($row['start_time']);
        $formatted_time = $time->format('g:ia');
        
        echo "<div class='list-group-item list-group-item-action'>
                <div class='d-flex w-100 justify-content-between'>
                <h5 class='mb-1'>Reminder regarding appointment no: <span class='poppins-semibold'>{$row['appointment_id']}</span></h5>
                <small>{$row['created_at']}</small>
                </div>
                <p class='mb-1'>Your appointment is scheduled for
                    <span class='poppins-semibold'>{$formatted_date}</span>
                    at
                    <span class='poppins-semibold'>{$formatted_time}</span>. See your there!
                </p>
        </div>";
    }
?>
