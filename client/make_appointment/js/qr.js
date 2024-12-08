function generateUUID() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

function handleAppointment(obj){
    const baseURL = window.location.origin;
    if(obj.studID !== ''){
        localStorage.removeItem("saveButtonDisabled");
        localStorage.setItem("transactionInfo", JSON.stringify(transactionInfo));
        window.location.href = `${baseURL}/ams/client/make_appointment/qrcode.php`;
    }else{
        alert("Empty Fields");
    }
}

//start
const transactionInfo = {
    appointmentID: "",
    studID: "",
    amount: "",
    date: "",
    shift: "",
    items: [] 
}

document.getElementById("clientForm").addEventListener("submit", (e)=>{
    e.preventDefault();
    let studId = document.getElementById("studId").value;
    let amountEl = document.getElementById("amount").value;
    let appt_date = document.getElementById("appt_date").value;
    let appt_shift = document.getElementById("appt_shift").value;
    let checkboxes = Array.from(document.querySelectorAll("input[type='checkbox']:checked")).map((elem) => elem.value);
    
    let totalAmount = 0;
    totalAmount = Number(amountEl);

    if(totalAmount !== 0){
        transactionInfo.appointmentID = generateUUID();
        transactionInfo.studID = parseInt(studId, 10);
        transactionInfo.amount = parseFloat(totalAmount).toFixed(2);
        transactionInfo.items = checkboxes;
        transactionInfo.date = appt_date;
        transactionInfo.shift = appt_shift;
        handleAppointment(transactionInfo);
    }else{
        alert("Add Items before submitting");
    }
})

