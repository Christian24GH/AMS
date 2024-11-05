const transactionInfo = {
    studID: "",
    studFirstname: "",
    studMiddle: "",
    studLastname: "",
    amount: "",
    items: []
};
document.getElementById("clientForm").addEventListener("submit", (e)=>{
    e.preventDefault();
    let studId = document.getElementById("studId").value;
    let studFirstname = document.getElementById("studFirstname").value;
    let studMiddle = document.getElementById("studMiddle").value;
    let studLastname = document.getElementById("studLastname").value;
    let amount = document.getElementById("amount").value;
    
    let selector = document.getElementById("items").selectedOptions;
    let items = Array.from(selector).map(({value}) => value);

    transactionInfo.studID = studId;
    transactionInfo.studFirstname = studFirstname;
    transactionInfo.studMiddle = studMiddle;
    transactionInfo.studLastname = studLastname;
    transactionInfo.amount = amount;
    transactionInfo.items = items;
    //fill transactInfo
    
    console.log(transactionInfo);
    generateQr(transactionInfo);
})

function generateQr(obj){
    let qr = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${JSON.stringify(obj)}`;
    
    let container = document.getElementById("qr_container");
    clearChild(container);
    let img = document.createElement("img");
    container.append(img);
    img.src = qr;
}
function clearChild(con){
    while(con.hasChildNodes()){
        con.removeChild(con.firstChild);
    }
}