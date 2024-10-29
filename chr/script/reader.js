let start_scan_btn = document.getElementById("start-scan-btn");
let reader_el = document.getElementById("reader");

const studObj = {
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
    return transaction = JSON.parse(text);
}

function display_result(transaction){
    let student_id = document.getElementById("student-id");
    let student_name = document.getElementById("student-name");
    let item_list = document.getElementById("item-list");
    let payment_amount = document.getElementById("payment-amount");

    let name = `${transaction["studLN"]}, ${transaction["studFN"]} ${transaction["studM"]}`;

    let studID = document.createTextNode(transaction["studID"]);
    let studName = document.createTextNode(name);
    let amount = document.createTextNode(transaction["amount"]);

    student_id.appendChild(studID);
    student_name.appendChild(studName);
    payment_amount.appendChild(amount);

    transaction["items"].forEach(el => {
        item_list.appendChild(document.createTextNode(el));
        item_list.appendChild(document.createElement("br"));
    });

    studObj["studID"] = transaction["studID"];
    studObj["studLN"] = transaction["studLN"];
    studObj["studFN"] = transaction["studFN"];
    studObj["studM"] = transaction["studM"];
    studObj["amount"] = transaction["amount"];
    studObj["items"] = transaction["items"];
}

function restart(){
    let student_id = document.getElementById("student-id");
    let student_name = document.getElementById("student-name");
    let item_list = document.getElementById("item-list");
    let payment_amount = document.getElementById("payment-amount");

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
    const fd = new FormData();
    if(studObj["studID"]){
        fd.append('studID', studObj["studID"]);
        fd.append('studLN', studObj["studLN"]);
        fd.append('studFN', studObj["studFN"]);
        fd.append('studM', studObj["studM"]);
        fd.append('amount', studObj["amount"]);
        
        studObj["items"].forEach(el=>{
            fd.append("items[]", el)
        });

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
        alert(data.status);
    });
}

document.getElementById("rescan").addEventListener("click", restart)
document.getElementById("approve").addEventListener("click", approve);

