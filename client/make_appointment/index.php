<!DOCTYPE html>
<html lang="en">
<head>
    <?php
        $root = $_SERVER["DOCUMENT_ROOT"];
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $base_url = $protocol . '://' . $host ."/ams/client";
        define("BASE_URL", $base_url);
    ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/idx_appoint.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/global/css/media-query.css">
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/global/css/client.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/global/css/fonts.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/node_modules/bootstrap/dist/css/bootstrap.min.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" >
    <title>Appointment Management System</title>

    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/calendar.css"/>
</head>
<body class="poppins-regular">
    <?php
        include "$root/AMS/client/global/components/session.php";    
        include "$root/AMS/client/global/components/header.php";
        include "$root/AMS/client/global/components/left_nav.php";
            
    ?>
    <input id='uid' type="hidden" value="<?php echo $_SESSION['stud_id']?>">
    <div class="modal fade" id="appt" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Add Appointment</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <?php
                    include 'mk_appt.php';
                ?>
            </div>
            </div>
        </div>
    </div>
    
    <div class="container-fluid justify-content-center m-0 p-2">
        <h3 class='text-center mt-3'>Add Appointment Schedule</h3>
        <div id="calendar" class=""></div>    
    </div>
    
    <script src="<?php echo BASE_URL;?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src='js/qr.js'></script>   
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
        const today = new Date();
        var startDate = new Date(today);
        startDate.setDate(today.getDate() + 1);

        var endDate = new Date(today);
        endDate.setDate(today.getDate() + 8);

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var datesWithEvents = new Set();
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap5',
                expandRows: true,
                validRange:{
                    start: startDate.toISOString().split('T')[0],
                    end: endDate.toISOString().split('T')[0]
                },
                headerToolbar: {
                    left: 'title',          
                    right: 'dayGridMonth,dayGridWeek,listWeek prev,today,next'
                },
                selectAllow: function(date) {
                    var isSunday = date.start.getDay() === 0;
                    return !isSunday;
                },
                events: function(info, successCallback, failureCallback) {
                    let uid = document.getElementById("uid").value;
                    fetch('requestHandler/fetch_ev.php',{
                        method: "post",
                        body: JSON.stringify({studID: uid})
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        successCallback(data);
                        
                        // Add each event's date to the datesWithEvents set
                        data.forEach(event => {
                            const date = new Date(event.start).toISOString().split('T')[0];
                            datesWithEvents.add(date);
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching events:', error);
                    });
                },
                dateClick: function(date){
                    let clickedDate = new Date(date.dateStr);
                    console.log(clickedDate.getDay());
                    if(!(clickedDate.getDay() === 0)){
                        let appt_date = document.getElementById("appt_date");
                        var myModal = new bootstrap.Modal(document.getElementById('appt'));
                        
                        const eventDate = date.dateStr;
                        if (!datesWithEvents.has(eventDate)) {
                            appt_date.value = date.dateStr;
                            myModal.show();
                        } else {
                            alert('You have pending appointments on that date!');
                        }
                    }
                }
            });
            calendar.render();
        });
    </script>
</body>
</html>