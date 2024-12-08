function initializeCalendar() {
    const today = new Date();
    let startDate = new Date(today);
    startDate.setDate(today.getDate() + 1);

    let endDate = new Date(today);
    endDate.setDate(today.getDate() + 8);

    const calendarEl = document.getElementById('calendar');
    const datesWithEvents = new Set();

    const slotsData = [];

    async function getSlots(){
        try {
            const response = await fetch('requestHandler/fetch_slot.php'); // Replace with your endpoint
            if (!response.ok) {
                throw new Error('Failed to fetch slots data');
            }
            const result = await response.json();
            result.forEach(data => {
                slotsData.push({
                    'date' : data.date,
                    'slot' : data.slot // Map date to available slots
                });
            });
        } catch (error) {
            console.error('Error fetching slots:', error);
        }
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        timeZone: 'UTC',
        themeSystem: "bootstrap5",
        initialView: 'dayGridMonth',
        expandRows: false,
        nowIndicator: true,
        validRange: {
            start: startDate.toISOString().split('T')[0],
            end: endDate.toISOString().split('T')[0],
        },
        headerToolbar: {
            left: 'title',
            right: 'dayGridMonth,dayGridWeek,listWeek prev,today,next',
        },
        dayCellDidMount: function (info) {
            let container = info.el.querySelectorAll(".fc-daygrid-day-bottom");
            let containerDate = info.date.toISOString().split('T')[0];
            
            const slotData = slotsData.find(data => data.date === containerDate);
            
           // Get today's date in YYYY-MM-DD format
            let today = new Date();
            today.setDate(today.getDate()+1);
            // Only display the upcoming day slot
            if (containerDate >= today.toISOString().split('T')[0]) { // Compare the current day
                container.forEach(el => {
                    if (slotData) {
                        let div = document.createElement('div');
                        let left = document.createElement('div');
                        let right = document.createElement('div');
                        left.textContent = `Slots`;
                        right.textContent = `${slotData.slot}/600`;
                        div.append(left);
                        div.append(right);
                        div.classList.add('slotEl', "rounded-1", "p-1", "bg-info-subtle", "text-info-emphasis");
                        el.append(div);
                        
                    }
                });
            }
        },
        selectAllow: function (date) {
            return date.start.getDay() !== 0;
        },
        events: function (info, successCallback, failureCallback) {
            const uid = document.getElementById("uid").value;

            fetch('requestHandler/fetch_ev.php', {
                method: "POST",
                body: JSON.stringify({ studID: uid }),
            })
            .then(response => response.json())
            .then(data => {
                successCallback(data);
                data.forEach(event => {
                    const date = new Date(event.start).toISOString().split('T')[0];
                    datesWithEvents.add(date);
                });
            })
            .catch(error => {
                console.error('Error fetching events:', error);
            });
        },
        dateClick: function (date) {
            const clickedDate = new Date(date.dateStr);
        
            if (clickedDate.getDay() !== 0) {
                const appt_date = document.getElementById("appt_date");
                const myModal = new bootstrap.Modal(document.getElementById('appt'));
        
                // Find slot data for the clicked date
                const slotData = slotsData.find(data => data.date === date.dateStr);
        
                // Check if slots are full
                if (slotData && slotData.slot >= 600) {
                    alert("No slots left for this date.");
                    return;
                }
        
                const eventDate = date.dateStr;
        
                // Check if the user already has an appointment on this date
                if (datesWithEvents && datesWithEvents.has(eventDate)) {
                    alert('You have pending appointments on that date!');
                    return;
                }
        
                // Proceed to open the modal
                appt_date.value = eventDate;
                myModal.show();
            }
        },
    });
    
    getSlots().then(()=>{
        calendar.render();
    })
}

document.addEventListener('DOMContentLoaded', initializeCalendar);