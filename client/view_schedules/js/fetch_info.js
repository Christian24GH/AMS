(()=>{
    let studIDEl = document.getElementById("studID");
    let stud_nameEl = document.getElementById("stud_name");
    let appt_dateEl = document.getElementById("appt_date");
    let appt_amountEl = document.getElementById("amount");
    let appt_items = document.getElementById("appt_items");
    let qLengthEl = document.getElementById("qLength");
    let appointment_id = document.getElementById("appointment_id");
    
    const transactionInfo = {
        appointment_id: "",
        studID: "",
        amount: "",
        date: "",
        shift: "",
        items: [] 
    }

    async function fetch_appt_data(){
        let appt_id = document.getElementById("appt_id").value;
        let baseURL = window.location.origin;
        
        try{
            let response = await fetch(`${baseURL}/ams/client/view_schedules/requestHandler/fetch_appt_info.php`, {
                method: "post",
                headers: {"Content-Type":"application/json"},
                body: JSON.stringify({
                    appt_id: appt_id
                })
            })
            let data = await response.json();
            assignValues(data);
            getQrcode(transactionInfo);
        }catch(error){
            console.log("Error fetching appointment data:", error);
        }
        
    }
    function getQrcode(obj){
        let img = document.getElementById("qr_code");
        try {
            const response = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${JSON.stringify(obj)}`;
            if (!response) {
                throw new Error("Error: Service Connection Failed");
            }
            img.src = response;
            img.addEventListener("load", hideplaceholder);
        } catch (error) {
            console.error("QR code generation failed:", error);
        }
        loadingScreen("hide");
    }
    function assignValues(data){
        console.log(data);
        let first_name = data.first_name || "";
        let middle_name = data.middle_name ? data.middle_name + " " : ""; 
        let last_name = data.last_name || "";

        studIDEl.appendChild(document.createTextNode(data.stud_id));
        stud_nameEl.textContent = `${first_name} ${middle_name}${last_name}`.trim();
        

        appt_amountEl.append(document.createTextNode(data.amount));
        qLengthEl.append(document.createTextNode(data.queue_length));
        appointment_id.value = data.appointment_id;

        console.log(data.status);
        let notQue = document.getElementById("notQue");
        let inQue = document.getElementById("inQue");
        if (data.status !== "Queued") {
            inQue.classList.add("d-none"); 
            notQue.classList.remove("d-none"); 
            
            const div = document.createElement("div");
            div.textContent = data.status;
            notQue.append(div);
        } else {
            notQue.classList.add("d-none"); 
            inQue.classList.remove("d-none"); // Show the in-queue element if hidden
        
            // Display appointment details in appt_dateEl (ensure appt_dateEl is defined)
        }
        appt_dateEl.append(
            document.createTextNode(
                `${formatDate(data.appointment_date)} / ${data.shift_type} Shift ${formatTime(data.start_time)} - ${formatTime(data.end_time)}`
            )
        );
        const itemNames = Array.isArray(data.item_names) ? data.item_names : [data.item_names];
        itemNames.forEach(name => {
            const li = document.createElement("li");
            li.textContent = name;
            appt_items.appendChild(li);
        });
        
        console.log(data);

        transactionInfo.appointment_id = data.appointment_id;
        
        transactionInfo.studID = parseInt(data.stud_id, 10);
        transactionInfo.amount = parseFloat(data.amount).toFixed(2); 

        transactionInfo.items = decodeURIComponent(data.items).split(" ");

        transactionInfo.date = data.appointment_date;
        transactionInfo.shift = data.shift_type || "";

        console.log("Formatted Transaction Info for QR:", transactionInfo);
    }
    
    function formatTime(timeString) {
        const [hour, minute] = timeString.split(':').map(Number); // Split and convert to numbers
        const period = hour >= 12 ? 'pm' : 'am';
        const formattedHour = hour % 12 || 12; // Convert to 12-hour format
        return `${formattedHour}:${minute.toString().padStart(2, '0')}${period}`;
    }

    function formatDate(dateString) {
        const [year, month, day] = dateString.split('-');
        return `${month}-${day}-${year}`;
    }
    function loadingScreen(state){
        const loadingOverlay = document.getElementById("loadingScreen");
        if (state === "show") {
            loadingOverlay.style.display = "flex"; // Shows the loading screen
        } else if (state === "hide") {
            loadingOverlay.style.display = "none"; // Hides the loading screen
        }
    }
    function hideplaceholder(){
        document.getElementById("qrPlaceholder").classList.add("d-none");
        //document.getElementById("buttonPlaceholder").classList.add("d-none");

        document.getElementById("qr_code").classList.remove("d-none");
        //document.getElementById("saveButton").classList.remove("d-none");
    }
    document.getElementById("delbtn").addEventListener("click", ()=>{
        let appointment_id = document.getElementById("appointment_id");
        console.log(appointment_id.value);
        const baseUrl = window.location.origin;
        fetch(`${baseUrl}/ams/client/view_schedules/requestHandler/delete_appt.php`, {
            method: 'post',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ appointment_id: appointment_id.value })
        })
        .then(response => {
            return response.json();
        })
        .then(data => {
            if(data.status === "ok"){
                window.location.href = baseUrl + "/ams/client/view_schedules/";
            }else{
                alert("Appointment Deletion Failed");
            }
        });
    })
    window.addEventListener("load", fetch_appt_data);
})();