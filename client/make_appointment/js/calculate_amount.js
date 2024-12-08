(()=>{
    const inventory = JSON.parse(window.sessionStorage.getItem("inventory"));
    let amountEl = document.querySelector("#amount");
    let checkboxes = document.querySelectorAll("input[type=checkbox][name=cbox]");
    let total = 0;
    checkboxes.forEach(box=>{
        box.addEventListener("change", (e)=>{
            let item_name = e.target.dataset.name;

            if(e.target.checked === true){
                let item = inventory.find(e=>e.item_name === item_name)
                if(item){
                    console.log(item.item_name);
                    total += Number(item.item_price);
                }
            }

            if(e.target.checked === false){
                let item = inventory.find(e=>e.item_name === item_name)
                if(item){
                    console.log(item.item_name);
                    total -= Number(item.item_price);
                    total = Math.max(total, 0);
                }
            }
            amountEl.value = total;
        })
    })
    function start(){
        total = 0;
        checkboxes.forEach(box=>{
            box.checked === false;
        })
    }
    amountEl.value = total;
    window.addEventListener("load", start());
})();