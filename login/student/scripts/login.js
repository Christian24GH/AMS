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
    console.log("click")
    let studID_fieldel = document.getElementById("id_field");
    // Listen for input event on the field
    let password_fieldel = document.getElementById("password_field");

    if(studID_fieldel.value && password_fieldel.value){
        validate(studID_fieldel.value, password_fieldel.value);
    }else{
        studID_fieldel.classList.add("is-invalid");
        password_fieldel.classList.add("is-invalid");
    }
});

function validate(id, pass){
    let studIDel = document.getElementById("id_field");
    let passwordel = document.getElementById("password_field");
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
            window.location.href = `${baseUrl}/ams/client/dashboard/`;
        } else {
            document.querySelector(".invalid-feedback").style.display = "block";
            studIDel.classList.add("is-invalid");
            passwordel.classList.add("is-invalid");
        }
    });
}