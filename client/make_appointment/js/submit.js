function generateQr(obj) {
    try{
        const response = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${JSON.stringify(obj)}`;
        if(!response){
            throw new Error(`Error: Service Connection Failed`);
        }
        return response;
        
    }catch(error){
        console.error('QR code generation failed:' + error);
        return null;
    }
}

function upload_appointment_data(obj){
    let baseURL = window.location.origin;
    fetch(`${baseURL}/AMS/client/make_appointment/requestHandler/saveAppointment.php`,{
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({
            studID: obj.studID,
            studFirstname: obj.studFirstname,
            studMiddle: obj.studMiddle,
            studLastname: obj.studLastname,
            amount: obj.amount,
            itemlist: obj.items,
            date: obj.date,
            shift: obj.shift
        })
    })
    .then(response =>{return response.json()})
    .then(result=>{
        if(!result.ok){
            throw new Error("Error:"+ result.error);
        }
        alert("Data Inserted");
        disableSaveButton();
    })
    .catch(err=>{
        console.log(err);
    })
}
function disableSaveButton() {
    const saveButton = document.getElementById("save_appointment");

    // Disable the button
    saveButton.classList.add("disabled");

    // Store the disabled state in localStorage
    localStorage.setItem("saveButtonDisabled", "true");
}
function appendQR(qr){
    let container = document.getElementById("qr_container");
    let img = document.createElement("img");
    
    //clears child
    while(container.hasChildNodes()){
        container.removeChild(container.firstChild);
    }

    container.append(img);
    img.src = qr;
    img.classList.add('card-img-top');
    setTimeout(hideplaceholder, 2000);
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
    document.getElementById("qrPlaceholder").classList.add("d-none");   // Hide QR placeholder
    document.getElementById("btnPlaceholder").classList.add("d-none");
    document.getElementById("qr_container").classList.remove("d-none");   // Show QR code container
    document.getElementById("save_appointment").classList.remove("d-none");     // Show save button
}

function getInfo(){
    const retrievedData = localStorage.getItem("transactionInfo");
    if (retrievedData) {
        // Convert the JSON string into a JavaScript object
        return JSON.parse(retrievedData);
    } else {
        // Return null or an empty object if there's no data in localStorage
        return null; // Or return {} if you prefer an empty object
    }
}
//start
function start(){
    const retrievedData = getInfo();
    const qr = generateQr(retrievedData);
    if(qr){
        loadingScreen("hide");
        appendQR(qr);
    }else{
        alert("Failed to generate QR");
    }
}



document.querySelector("#save_appointment").addEventListener("click", e=>{
    e.preventDefault();
    const transactionInfo = getInfo();
    if (transactionInfo) {
        upload_appointment_data(transactionInfo);
        console.log(transactionInfo);
    } else {
        console.log("No transaction data available.");
    }
})
window.addEventListener("load", start);
window.addEventListener("load", function () {
    const saveButton = document.getElementById("save_appointment");

    if (localStorage.getItem("saveButtonDisabled") === "true") {
        saveButton.classList.add("disabled");
    }
});