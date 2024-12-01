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
        expandRows: true,
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
            console.log('Date:', info.date.toISOString().split('T')[0]);
            
            const slotData = slotsData.find(data => data.date === containerDate);
            
            container.forEach(el=>{
                if(slotData){
                        el.textContent = `Slots: ${slotData.slot}/600`;
                }
            })
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

                const eventDate = date.dateStr;
                if (!datesWithEvents.has(eventDate)) {
                    appt_date.value = date.dateStr;
                    myModal.show();
                } else {
                    alert('You have pending appointments on that date!');
                }
            }
        },
    });
    
    getSlots().then(()=>{
        calendar.render();
    })
}

document.addEventListener('DOMContentLoaded', initializeCalendar);