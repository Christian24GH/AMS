let qrBox = document.getElementById("qrBox");
let text = document.getElementById("text");

document.getElementById("generateBtn").addEventListener("click", ()=>{
    qrBox.src = `https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=${text.value}`;
});