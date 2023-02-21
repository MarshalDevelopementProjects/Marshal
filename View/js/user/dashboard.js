console.log(jsonData[0])

const toastContainer = document.querySelector('.toast-notification');
const notificationCloseBtn = document.querySelector('.close-btn');
var toastNotificationTimeout = 5000;

setTimeout(() => {
    toastContainer.classList.add('active');
}, 1000);
setTimeout(() => {
    toastContainer.classList.remove('active');
}, toastNotificationTimeout);

// console.log(toastNotificationTimeout)

notificationCloseBtn.addEventListener('click', () => toastContainer.classList.remove('active'));


const notificationPopupBtn = document.querySelector('.notification-bell-btn')
const notificationPopup = document.querySelector('.notification-popup-container');
const notificationPopupCloseBtn = document.querySelector('.notification-popup-close-btn');
const notificationPopupContainer = document.querySelector('.notification-popup-container');
const container = document.querySelector('.container');

notificationPopupBtn.addEventListener('click', () => notificationPopup.classList.add('active'));
notificationPopupCloseBtn.addEventListener('click', () => notificationPopup.classList.remove('active'));
// notificationPopup.addEventListener('click', () => notificationPopup.classList.remove('active'))


const navbarNewProjectBtn = document.querySelector('.navbar-new-project');
const createProjectPopup = document.querySelector('.create-project-container');
const createProjectCloseBtn = document.querySelector('.create-project-popup-close');

navbarNewProjectBtn.addEventListener('click', () => createProjectPopup.classList.add('active'));
createProjectCloseBtn.addEventListener('click', () => createProjectPopup.classList.remove('active'));

$notifications = [];

// check notifications for this user
const notificationArea = document.querySelector('.notifications'),
toastNotificationDetails = document.querySelector('.details'),
projects = document.querySelector('.projects');

// this onLoad function receive notifications and load page data
function onLoad(){
    // load notifications
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
      console.log(data);
      let notifications = data['message']
      let code = "";

      notifications.forEach(notification => {

            if(notification['type'] === "request"){
                code += `<div class="request-notification">
                            <hr>
                            <div class="notification-details">
                                <img src="/View/images/Picture5.png" alt="notificaton sender image">
                                <div class="content">
                                    <div class="sender-and-project">
                                        <h4>${notification['senderId']}</h4>
                                    </div>
                                    <div class="request-content">
                                        <h5>Project invite</h5>
                                        <p class="request-message">${notification['message']}</p>

                                        <div class="responses">
                                            <a href="#"><button type="submit" id="rejectInviteBtn">Reject</button></a>
                                            <a href="http://localhost/public/user/join?data1=${notification['projectId']}&data2=${notification['id']}"><button type="submit" id="acceptInviteBtn">Accept</button></a>
                                            
                                        </div>
                                    </div>
                                    <div class="date-and-project">
                                        <p class="send-date">${notification['sendTime']}</p>
                                        <p class="notification-project">Mentcare Center Web App</p>
                                    </div>
                                </div>
                            </div>
                        </div>`
            }else{
                code += `<div class="notification">
                        <hr>
                        <a href="http://localhost/public/user/clicknotification?data=${notification['id']}">
                            <div class="notification-details">
                                <img src="/View/images/Picture5.png" alt="notificaton sender image">
                                <div class="content">
                                    <div class="sender-and-project">
                                        <h4>${notification['senderId']}</h4>
                                    </div>
                                    <p class="notification-content">${notification['message']}</p>
                                    <div class="date-and-project">
                                        <p class="send-date">${notification['sendTime']}</p>
                                        <p class="notification-project">Mentcare Center Web App</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                        
                    </div>`
            }

            
      });
      notificationArea.innerHTML = code;
    //   console.log(code)

    //   set toast notification
      if(notifications[0] == null){
        toastNotificationTimeout = 1000
      }
      let toastNotificationCode = `<div class="notification-top">
                                    <h3 class="sender-name">${notifications[0]['senderId']}</h3>
                                    <button type="submit" class="close-btn"><i class="fa fa-times" aria-hidden="true"></i></button>
                                </div>
                                <p class="notification-msg">${notifications[0]['message']}</p>
                                <div class="notification-bottom">
                                    <p class="send-date"><span class="time">${notifications[0]['sendTime']}</span></p>
                                    <p class="notification-project-name"> <i>- Marshal Project -</i> </p>
                                </div>`
        
      toastNotificationDetails.innerHTML = toastNotificationCode

    })
    .catch(error => {
      console.error(error);
    })

    // load project data
    projectCardsCode = ""
    jsonData.forEach(project => {
        projectCardsCode += `<div class="project-card">
                                <a href="http://localhost/public/user/project?id=${project['id']}" class="clickable-project">
                                    <p class="project-field ">${project['field']}</p>
                                <h3 class="project-name">${project['project_name']}</h3>

                                <div class="tasks">
                                    <p>Ongoing Work</p>
                                    <ul class="task-list">
                                        <li>Design UI for login page</li>
                                        <li>Fixing #15 bug</li>
                                        <li>Develop API</li>
                                        <li>Create reports for #3 week</li>
                                    </ul>
                                </div>
                                <p class="team">Team</p>
                                <div class="card-bottom">
                                    <div class="member-images">
                                        <img class="image first" src="/View/images/Picture1.png" alt="Picture1">
                                        <img class="image rest1" src="/View/images/Picture2.png" alt="Picture2">
                                        <img class="image rest2"src="/View/images/Picture3.png" alt="Picture3">
                                        <img class="image rest3"src="/View/images/Picture4.png" alt="Picture4">
                                        <img class="image rest4"src="/View/images/Picture5.png" alt="Picture5">
                                    </div>
                                    <!-- <button type="submit">Get Info</button> -->
                                    
                                </div>
                                </a>
                            </div>`
    })

    projects.innerHTML = projectCardsCode
}




const dashboard = document.querySelector('.dashboard');
const newProject = document.querySelector('.new-project');
const profile = document.querySelector('.profile');
const settings = document.querySelector('.settings');
const sketchIdea = document.querySelector('.sketch-idea');

dashboard.addEventListener('click', function(){
    dashboard.classList.add('active');
    profile.classList.remove('active');
    newProject.classList.remove('active');
    settings.classList.remove('active');
    sketchIdea.classList.remove('active');

})
newProject.addEventListener('click', function(){
    dashboard.classList.remove('active');
    profile.classList.remove('active');
    newProject.classList.add('active');
    settings.classList.remove('active');
    sketchIdea.classList.remove('active');

    createProjectPopup.classList.add('active');

})
profile.addEventListener('click', function(){
    dashboard.classList.remove('active');
    profile.classList.add('active');
    newProject.classList.remove('active');
    settings.classList.remove('active');
    sketchIdea.classList.remove('active');


})
settings.addEventListener('click', function(){
    dashboard.classList.remove('active');
    profile.classList.remove('active');
    newProject.classList.remove('active');
    settings.classList.add('active');
    sketchIdea.classList.remove('active');

})

sketchIdea.addEventListener('click', function(){
    dashboard.classList.remove('active');
    profile.classList.remove('active');
    newProject.classList.remove('active');
    settings.classList.remove('active');
    sketchIdea.classList.add('active');

    window.location.href = "./index.html";
})



// Send form data for creating a new project

