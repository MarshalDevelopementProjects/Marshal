console.log(jsonData);
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