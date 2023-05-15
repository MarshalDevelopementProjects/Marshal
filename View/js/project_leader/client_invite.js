{/* <div class="client-invite-popup">
            <h3>Invite client</h3>
            <label for="username">Username</label>
            <br>
            <input type="text" name="username" placeholder="Enter username">
            <div class="buttons">
                <button type="submit" id="clientInviteBtn">Send</button>
                <button type="submit" id="clientInvitecloseBtn">Close</button>
            </div>
        </div> */}


const clientInvitePopup = document.querySelector('.client-invite-popup'),
        clientUserName = document.querySelector('.client-invite-popup input'),
        cancelBtn = document.getElementById('clientInvitecloseBtn'),
        inviteBtn = document.getElementById('clientInviteBtn'),
        addClientBtn = document.getElementById('add-client-btn');

addClientBtn.addEventListener('click', function() {
    clientInvitePopup.classList.add('active');
})

cancelBtn.addEventListener('click', function(){
    clientInvitePopup.classList.remove('active');
})

inviteBtn.addEventListener('click', function(){
    if(clientUserName.value){
        fetch(
            "http://localhost/public/project/leader/client/invite",
            {
              withCredentials: true,
              credentials: "include",
              mode: "cors",
              method: "POST",
              body: clientUserName.value,
              headers: {
                'Content-Type': 'application/json',
              }        
            }
          )
          .then(response => response.json())
            .then(data => {
                console.log(JSON.stringify(data));
                clientInvitePopup.classList.remove('active')
            })
          .catch(function(error){console.log(error)})
    }else{
        clientInvitePopup.classList.remove('active');
    }
})