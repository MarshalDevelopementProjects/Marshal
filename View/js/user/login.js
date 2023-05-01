console.log(jsonData);
const passwordField = document.querySelector("#password");
const showHideButton = document.querySelector(".showHidePw");

showHideButton.addEventListener("click", () => {
  if (passwordField.type === "password") {
    passwordField.type = "text";
    showHideButton.classList.replace("uil-eye-slash", "uil-eye");
  } else {
    passwordField.type = "password";
    showHideButton.classList.replace("uil-eye", "uil-eye-slash");
  }
});

const ErrorDiv = document.getElementById("error");
const ErrorMSG = document.getElementById("error_msg");

ErrorMSG.innerHTML = `${jsonData.errors}`;
console.log(`${jsonData.errors}`);
if(ErrorMSG.innerHTML != '' && ErrorMSG.innerHTML !='undefined'){
    ErrorDiv.classList.add("active");
    setTimeout(function() {
        ErrorDiv.classList.remove("active");
        ErrorMSG.innerHTML = '';
      }, 6000);
}