let start_scan_btn = document.getElementById("start-scan-btn");
let reader_el = document.getElementById("reader");

const studObj = {
    appointment_id:"",
    studID:"",
    studLN:"",
    studFN:"",
    studM:"",
    amount:"",
    items: []
}
start_scan_btn.addEventListener("click", ()=>{

    if(reader_el.classList.contains("hide")){
        reader_el.classList.remove("hide");
        start_scan_btn.classList.add("hide");
    }

    camera_start();
});

function camera_start(){
    if(!(reader_el.classList.contains("hide"))){
        Html5Qrcode.getCameras().then(devices=>{
            if (devices && devices.length) {
                let cameraId = devices[0].id;
                
                const config = {
                    fps: 20, 
                    qrbox: {width: 250, height: 250}, 
                    aspectRatio: 1.0,
                    disableFlip: true
                };

                const scanner = new Html5Qrcode("reader");
                scanner.start(cameraId, config, (result)=>{
                    scanner.stop();
                    display_result(filter_result(result));
                    scanner.clear();
                });
            }
        })
    }
}

function filter_result(text){
    console.log("Result: ", JSON.parse(text));
    return transaction = JSON.parse(text);
}
async function fetch_name(studID) {
    const response = await fetch('script/fetch/get_name.php', {
        method: 'post',
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ studID: studID })
    });
    return response.json(); // Await and parse the response as JSON
}

async function display_result(transaction) {
    let student_id = document.getElementById("student-id");
    let appointment_id = document.getElementById("appointment_id");
    let student_name = document.getElementById("student-name");
    let item_list = document.getElementById("item-list");
    let payment_amount = document.getElementById("payment-amount");

    const fullname = await fetch_name(transaction["studID"]);
    
    let name = `${fullname['stud_ln']}, ${fullname['stud_fn']} ${fullname['stud_m'] || ''}`.trim();

    student_id.appendChild(document.createTextNode(transaction["studID"]));
    student_name.appendChild(document.createTextNode(name));
    payment_amount.appendChild(document.createTextNode(transaction["amount"]));
    appointment_id.appendChild(document.createTextNode(transaction["appointment_id"]));
    
    
    const inventory = JSON.parse(window.sessionStorage.getItem("inventory"));
    transaction["items"].forEach(el => {
        const item = inventory.find(item => item.item_id == el);

        if (item) {
            const name = item.item_name;
            console.log(name);
            
            item_list.appendChild(document.createTextNode(name));
            item_list.appendChild(document.createElement("br"));
        }
    });
    console.log(transaction["items"]);
    console.log(inventory);
    studObj["appointment_id"] = transaction["appointment_id"];
    studObj["studID"] = transaction["studID"];
    studObj["studLN"] = fullname['stud_ln'];
    studObj["studFN"] = fullname['stud_fn'];
    studObj["studM"] = fullname['stud_m'];
    studObj["amount"] = transaction["amount"];
    studObj["items"] = transaction["items"];
}
function restart(){
    let student_id = document.getElementById("student-id");
    let student_name = document.getElementById("student-name");
    let item_list = document.getElementById("item-list");
    let payment_amount = document.getElementById("payment-amount");
    let appointment_id = document.getElementById("appointment_id");
    while(appointment_id.hasChildNodes()){
        appointment_id.removeChild(appointment_id.firstChild);
    }
    while(student_id.hasChildNodes()){
        student_id.removeChild(student_id.firstChild);
    }
    while(student_name.hasChildNodes()){
        student_name.removeChild(student_name.firstChild);
    }
    while(item_list.hasChildNodes()){
        item_list.removeChild(item_list.firstChild);
    }
    while(payment_amount.hasChildNodes()){
        payment_amount.removeChild(payment_amount.firstChild);
    }
    camera_start();
}
function approve(){
    //check if valid
    //if valid, proceed to call a sql insert
    let cashier_id = document.querySelector("#cashier_id").value;
    console.log(cashier_id);
    const fd = new FormData();
    if(studObj["studID"]){
        fd.append('appointment_id', studObj['appointment_id']),
        fd.append('studID', studObj["studID"]);
        fd.append('studLN', studObj["studLN"]);
        fd.append('studFN', studObj["studFN"]);
        fd.append('studM', studObj["studM"]);
        fd.append('amount', studObj["amount"]);
        fd.append('items', JSON.stringify(studObj['items']));
        fd.append('cashier_id', cashier_id);
        insert_data(fd);
        
    }else{
        alert("empty field");
    }
}
function insert_data(formdata) {
    fetch("script/fetch/insert.php", {
        method: "POST",
        body: formdata
    }).then(result=>{
        return result.json();
    }).then(data=>{
        if(data.result == "OK"){
            alert("Transaction Success");
            console.log(data.result);
            restart();
        }
    });
}

document.getElementById("rescan").addEventListener("click", restart)
document.getElementById("approve").addEventListener("click", approve);

