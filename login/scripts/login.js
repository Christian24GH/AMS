document.getElementById("login_form").addEventListener("submit",(e)=>{
    e.preventDefault();

    let studID_field = document.getElementById("id_field").value;
    let password_field = document.getElementById("password_field").value;

    if(studID_field && password_field){
        validate(studID_field, password_field);
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
        if(data.id == 1){
            if(data.password == 1){
                window.location.href = "../chr/index.php";
            }else{
                console.log("invalid password");
            }
        }else{
            console.log("invalid id");
        }
    });
}