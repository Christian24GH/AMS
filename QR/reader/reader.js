let readerDiv =  document.getElementById("reader");
let resultDiv = document.getElementById("result");
let resultText = document.getElementById("resultText");
let config = {width: 150, height: 150};

let scanner = new Html5QrcodeScanner("reader", { 
    qrbox: config,
    fps: 20,
    aspectRatio: 1.0,
    disableFlip: true
});

function onScanSuccess(decodedText) {
    let content = document.createTextNode(decodedText);
    resultText.appendChild(content);
    
    scanner.clear();
    readerDiv.classList.add("hide");
    if(resultDiv.classList.contains("hide")){
        resultDiv.classList.toggle("hide");
    }
}
  
function onScanFailure(error) {
    //console.log(scanner.getState());
}

function main(){
    scanner.render(onScanSuccess, onScanFailure);
    if(readerDiv.classList.contains("hide")){
        readerDiv.classList.remove("hide");
        resultDiv.classList.toggle("hide");
        while(resultText.firstChild){
            resultText.removeChild(resultText.lastChild);
        }
    }
}

document.getElementById("restart").addEventListener("click", ()=>{
    main();
});

main();