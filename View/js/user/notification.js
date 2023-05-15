const notificationArea = document.querySelector('.notifications')

function getNotification(){
    fetch(
        "http://localhost/public/user/notifications", 
        {
          withCredentials: true,
          credentials: "include",
          mode: "cors",
          method: "GET",
        }
    )
    .then(response => response.json())
    .then(data => {

      let notifications = data['message']
      notifications = notifications.reverse()

      console.log(notifications)
      let code = "";
      if(notifications != []){
        notifications.forEach(notification => {

            if(notification['type'] === "request"){
                code += `<div class="request-notification">
                            <hr>
                            <div class="notification-details">
                                <img src="${notification['sender_profile']}" alt="">
                                <div class="content">
                                    <div class="sender-and-project">
                                        <h4>${notification['sender_name']}</h4>
                                    </div>
                                    <div class="request-content">
                                        <h5>Project invitation</h5>
                                        <p class="request-message">${notification['message']}</p>

                                        <div class="responses">
                                            <a href="http://localhost/public/user/request/reject?data=${notification['id']}"><button type="submit" id="rejectInviteBtn">Reject</button></a>
                                            <a href="http://localhost/public/user/join?data1=${notification['project_id']}&data2=${notification['id']}"><button type="submit" id="acceptInviteBtn">Accept</button></a>
                                            
                                        </div>
                                    </div>
                                    <div class="date-and-project">
                                        <p class="send-date">${notification['send_time'].split(" ")[0]}</p>
                                        <p class="notification-project">Mentcare Center Web App</p>
                                    </div>
                                </div>
                            </div>
                        </div>`
            }else{
                code += `<div class="notification">
                        <hr>
                        <a href="http://localhost/public/user/clicknotification?data=${notification['id']}" style="text-decoration: none;">
                            <div class="notification-details">
                                <img src="${notification['sender_profile']}" alt="">
                                <div class="content">
                                    <div class="sender-and-project">
                                        <h4>${notification['sender_name']}</h4>
                                    </div>
                                    <p class="notification-content">${notification['message']}</p>
                                    <div class="date-and-project">
                                        <p class="send-date">${notification['send_time'].split(" ")[0]}</p>
                                        <p class="notification-project">${notification['project_name']}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        
                    </div>`
            }

            
      });
      }
      notificationArea.innerHTML = code;
    })
    .catch((error) => console.error(error))
}

getNotification()

function rejectRequest(notificationId){
    fetch(
        "http://localhost/public/user/request/reject?data=" + notificationId, 
        {
          withCredentials: true,
          credentials: "include",
          mode: "cors",
          method: "GET",
        }
    )
    .then(response => response.json())
    .then(data => {

      
    })
    .catch((error) => console.error(error))
}