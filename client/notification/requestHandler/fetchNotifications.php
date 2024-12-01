<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $root = $_SERVER['DOCUMENT_ROOT'];

    require $root . '/ams/client/notification/PHPMailer/src/Exception.php';
    require $root .'/ams/client/notification/PHPMailer/src/PHPMailer.php';
    require $root .'/ams/client/notification/PHPMailer/src/SMTP.php';
    include $root . "/ams/client/global/components/conn.php";

    session_start();
    $stud_id = $_SESSION['stud_id'];
    $stmt = $conn->prepare("SELECT * FROM getnotification WHERE stud_id = ?");
    $stmt->bind_param("i", $stud_id);  
    $stmt->execute();

    $result = $stmt->get_result();
    $notifications = [];

    while ($row = $result->fetch_assoc()) {
        $appointment_date = $row['appointment_date'];
        $formatted_date = date('m/d/y', strtotime($appointment_date));
        $time = new DateTime($row['start_time']);
        $formatted_time = $time->format('g:ia');

        if (!$row['is_sent']) {

            $topic = 'Reminder for your upcoming appointment';
            $message = "<div>Hello! Your appointment is scheduled for <b>{$formatted_date}</b> at <b>{$formatted_time}</b>. See you there!</div>";
           
            error_log("Preparing to send email for appointment ID: " . $row['appointment_id']);
            sendEmail($row['stud_email'], $topic, $message);

            $conn->query("UPDATE notifications SET is_sent = 1 WHERE appointment_id = '{$row['appointment_id']}'");
            if (!$conn->query($updateQuery)) {
                error_log("Error updating is_sent for appointment ID {$row['appointment_id']}: " . $conn->error);
            } else {
                error_log("is_sent updated successfully for appointment ID {$row['appointment_id']}");
            }
        }
        $notifications[] = [
            'appointment_id' => $row['appointment_id'],
            'formatted_date' => $formatted_date,
            'formatted_time' => $formatted_time,
            'notify_time' => $row['notify_time'],
        ];  
    }
    echo json_encode($notifications);

    function sendEmail($email, $subject, $message) {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'amsgroupcorporation24@gmail.com';
        $mail->Password = 'lxpuwyjfrwurwrqg';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;
        $mail->setFrom("amsgroupcorporation24@gmail.com");
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
    }
?>