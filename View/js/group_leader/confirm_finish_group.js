const confirmFinishPopup = document.querySelector('.confirm-finish-popup'),
    finishBtn = document.querySelector('.finish-group-task'),
    cancelBtn = document.querySelector('.cancel-btn'),
    groupFinishBtn = document.querySelector('.finish-btn'),
    message = document.querySelector('.success-message');

finishBtn.addEventListener('click', function(){
    rightPanel.classList.add('finish');
    confirmFinishPopup.classList.add('active');
})

cancelBtn.addEventListener('click', function(){
    rightPanel.classList.remove('finish');
    confirmFinishPopup.classList.remove('active');
});

groupFinishBtn.addEventListener('click', function(){
    fetch(`http://localhost/public/groupleader/finishgroup`, {
        withCredentials: true,
        credentials: "include",
        mode: "cors",
        method: "GET"
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        message.innerText = data.message;
    })
    .catch((error) => {
        console.error(error)
    })
    console.log("finished")
});