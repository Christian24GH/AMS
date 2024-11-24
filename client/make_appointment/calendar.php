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
    
    
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/global/css/media-query.css">
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/global/css/client.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/global/css/fonts.css"/>
    <link rel="stylesheet" href="<?php echo BASE_URL;?>/node_modules/bootstrap/dist/css/bootstrap.min.css"/>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>
    <link rel="stylesheet" href="css/calendar.css"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" >
    
    <title>Appointment Management System</title>
</head>
<body class="poppins-regular">
    <?php
        include "$root/AMS/client/global/components/session.php";    
        include "$root/AMS/client/global/components/header.php";
        include "$root/AMS/client/global/components/left_nav.php";
    ?>
    <div class="container-fluid border d-flex justify-content-center py-3 main-container h-100" style="width: 40rem;">
        <div id="calendar"></div>
    </div>
    <div id="event-list" style="margin: 20px;">
        <h3>Upcoming Events (Drag to Calendar)</h3>
        <div class="draggable-event" draggable="true" data-title='Event 1'>
            Event 1
        </div>
        <div class="draggable-event" draggable="true" data-title='Event 2'>
            Event 2
        </div>
        <div class="draggable-event" draggable="true" data-title='Event 3'>
            Event 3
        </div>
    </div>
    <script src="<?php echo BASE_URL;?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // FullCalendar setup
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                themeSystem: 'bootstrap5',
                editable: true,
                droppable: true,
                eventReceive: function(eventInfo) {
                    alert('Event added: ' + eventInfo.event.title);
                }
            });
            calendar.render();

            var draggableEvents = document.querySelectorAll('.draggable-event');
            draggableEvents.forEach(function(event) {
                event.addEventListener('dragstart', function(e) {
                    e.dataTransfer.setData('text/plain', event.getAttribute('data-title'));
                });
            });

            calendarEl.addEventListener('drop', function(event) {
                event.preventDefault();
                var title = event.dataTransfer.getData('text/plain'); 
                var date = calendar.getDate();
                
                if (date) {
                    // Add the event to the calendar
                    calendar.addEvent({
                        title: title,
                        start: date,
                        allDay: true
                    });
                }
            });

            calendarEl.addEventListener('dragover', function(event) {
                event.preventDefault();
            });
        });
    </script>

        
    </script>
</body>
</html>