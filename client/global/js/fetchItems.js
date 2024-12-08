(()=>{
    const inventory = window.sessionStorage;
    async function start(){
        try{
            const response = await fetch("requestHandler/fetchItems.php");
            if(!response.ok){
                throw "An Error has occured";
            }
            const result = await response.json();
            inventory.clear();
            inventory.setItem("inventory", JSON.stringify(result));
           console.log(inventory);
        }catch(e){
            console.log(e);
        }
        
    }
    window.addEventListener("load", start)
})()