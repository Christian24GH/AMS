let text = "studID:123456789,studLN:Doe,studFN:Koe,studM:Loe,items:#1234#5678#9889,amount:1600";
function filter_result(text){
    /*
        studID:123456789,
        studLN:Doe,
        studFN:Koe,
        studM:Loe,
        items:#1234#5678#9889,
        amount:1600
    */
    const transation = {
        studID, 
        studLN,
        studFN,
        studM,
        amount,
        items: []
    }

    let res = text.search("studID:");
    

   console.log(res);
}
function display_result(result){

}