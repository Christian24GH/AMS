let studID_fieldel = document.getElementById("id_field");
    // Listen for input event on the field
studID_fieldel.addEventListener("keydown", function (event) {
    // Allow only numbers, backspace, and arrow keys
    if (!/[0-9]/.test(event.key) && event.key !== "Backspace" && event.key !== "ArrowLeft" && event.key !== "ArrowRight") {
        event.preventDefault();
    }
});
document.getElementById("login_form").addEventListener("submit",(e)=>{
    e.preventDefault();

    let ID_field = document.getElementById("id_field").value;
    let password_field = document.getElementById("password_field").value;

    if(ID_field && password_field){
        validate(ID_field, password_field);
    }else{
        alert("Empty Fields");
    }
});

function validate(id, pass){
    const fd = new FormData();
    fd.append("id", id);
    fd.append("pass", pass);
    fetch("request/validate.php", {
        method: "POST",
        body: fd
    }).then(result=>{
        return result.json();
    }).then(data=>{
        if (data.id == 1 && data.password == 1) {
            const baseUrl = window.location.origin;
            window.location.href = `${baseUrl}/ams/chr/`;
        } else {
            alert("Invalid ID or Password");
        }
    });
}