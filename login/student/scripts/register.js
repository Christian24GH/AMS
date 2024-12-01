let studID_fieldel = document.getElementById("id_field");
let email_fieldel = document.getElementById("email_field");
let password_fieldel = document.getElementById("password_field");
let fn_field = document.getElementById("fn_field");
let m_field = document.getElementById("m_field");
let ln_field = document.getElementById("ln_field");
const invalidFeedback = document.querySelector(".invalid-feedback");
    // Listen for input event on the field
studID_fieldel.addEventListener("keydown", function (event) {
    // Allow only numbers, backspace, and arrow keys
    if (!/[0-9]/.test(event.key) && event.key !== "Backspace" && event.key !== "ArrowLeft" && event.key !== "ArrowRight") {
        event.preventDefault();
    }
});
document.getElementById("login_form").addEventListener("submit",(e)=>{
    e.preventDefault();
    if(allfieldfilled()){
        console.log("submitted");
        validate(studID_fieldel.value,
            password_fieldel.value,
            email_fieldel.value,
            fn_field.value,
            m_field.value || "",
            ln_field.value
        );
    }else{
        showInvalidFeedback("Please fill in all required fields.");
        markFieldsInvalid();
    }
});

function validate(id, pass, email, fn_field, m_field, ln_field){
    const fd = new FormData();
    fd.append("id", id);
    fd.append("pass", pass);
    fd.append("email", email);
    fd.append("fn", fn_field);
    fd.append("m", m_field);
    fd.append("ln", ln_field);
    
    fetch("request/register.php", {
        method: "POST",
        body: fd
    }).then(result=>{
        return result.json();
    }).then(data=>{
        if (data.status == 200) {
            alert("Account Successfully Created");
            const baseUrl = window.location.origin;
            window.location.href = `${baseUrl}/ams/client/dashboard/`;
        } else {
            showInvalidFeedback(data.message);
            markFieldsInvalid();
        }
    });
}
function allfieldfilled(){
    return (
        studID_fieldel.value &&
        password_fieldel.value &&
        email_fieldel.value &&
        fn_field.value &&
        ln_field.value
    );
    
}

function showInvalidFeedback(message) {
    invalidFeedback.textContent = message;
    invalidFeedback.style.display = "block";
}

function markFieldsInvalid() {
    studID_fieldel.classList.add("is-invalid");
    password_fieldel.classList.add("is-invalid");
    email_fieldel.classList.add("is-invalid");
    fn_field.classList.add("is-invalid");
    ln_field.classList.add("is-invalid");
    if (m_field.value === "") {
        m_field.classList.add("is-invalid");
    }
}