let sched_list = document.getElementById("sched_list");
sched_list.addEventListener("click", (e)=>{
    e.preventDefault();

    let targetElement = e.target.closest(".sched-items");
    if (targetElement) {
        let id = targetElement.dataset.appointmentId;
        if (id) {
            let baseURL = window.location.origin;
            window.location.href = `${baseURL}/ams/client/transaction_log/appt_info.php?id=${id}`;
            console.log("Clicked, id = " + id);
        } else {
            console.log("No appointmentId found on clicked element.");
        }
    }
})