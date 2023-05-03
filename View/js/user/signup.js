console.log(jsonData);
const pwFields = document.querySelectorAll(".password-field");

pwFields.forEach(pwField => {
    const showHidePw = pwField.parentElement.querySelector(".showHidePw");
    const eyeIcon = showHidePw.querySelector("i");

    showHidePw.addEventListener("click", () => {
        if (pwField.type === "password") {
            pwField.type = "text";
            eyeIcon.classList.replace("uil-eye-slash", "uil-eye");
        } else {
            pwField.type = "password";
            eyeIcon.classList.replace("uil-eye", "uil-eye-slash");
        }
    });
});

const ErrorDiv = document.getElementById("error");
const ErrorMSG = document.getElementById("error_msg");

ErrorMSG.innerHTML = `${jsonData.errors[0]}`;
if(ErrorMSG.innerHTML != '' && ErrorMSG.innerHTML !='undefined'){
    ErrorDiv.classList.add("active");
    setTimeout(function() {
        ErrorDiv.classList.remove("active");
        ErrorMSG.innerHTML = '';
      }, 6000);
}