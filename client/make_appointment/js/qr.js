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
    studID: "",
    studFirstname: "",
    studMiddle: "",
    studLastname: "",
    amount: "",
    date: "",
    shift: "",
    items: [] 
}

document.getElementById("clientForm").addEventListener("submit", (e)=>{
    e.preventDefault();
    let studId = document.getElementById("studId").value;
    let studFirstname = document.getElementById("studFirstname").value;
    let studMiddle = document.getElementById("studMiddlename").value;
    let studLastname = document.getElementById("studLastname").value;
    let amount = document.getElementById("amount").value;
    let appt_date = document.getElementById("appt_date").value;
    let appt_shift = document.getElementById("appt_shift").value;
    let checkboxes = Array.from(document.querySelectorAll("input[type='checkbox']:checked")).map((elem) => elem.value);
    
    transactionInfo.studID = studId;
    transactionInfo.studFirstname = studFirstname;
    transactionInfo.studMiddle = studMiddle;
    transactionInfo.studLastname = studLastname;
    transactionInfo.amount = amount;
    transactionInfo.items = checkboxes;
    transactionInfo.date = appt_date;
    transactionInfo.shift = appt_shift;
    handleAppointment(transactionInfo);
})

