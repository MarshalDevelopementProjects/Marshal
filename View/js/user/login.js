console.log(jsonData);

const container = document.querySelector(".container"),
        pwShowHide = document.querySelectorAll(".showHidePw"),
        pwFields = document.querySelectorAll(".password");

        //   js code to show/hide password and change icon
        pwShowHide.forEach(eyeIcon =>{
            eyeIcon.addEventListener("click", ()=>{
                pwFields.forEach(pwField =>{
                    if(pwField.type ==="password"){
                        pwField.type = "text";

                        pwShowHide.forEach(icon =>{
                            icon.classList.replace("uil-eye-slash", "uil-eye");
                        })
                    }else{
                        pwField.type = "password";

                        pwShowHide.forEach(icon =>{
                            icon.classList.replace("uil-eye", "uil-eye-slash");
                        })
                    }
                }) 
            })
        })

        var data = JSON.parse('<?= $data; ?>');
        console.log(data);

        const email = document.getElementById("email")
        const errorMessage = document.getElementById("error-message")

        if(data.error){
            errorMessage.classList.add('display-error')
            errorMessage.innerHTML = data.error

            email.value = data.email
        }