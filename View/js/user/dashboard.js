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






// calendor 

function lastMondayOfMonth(month, year) {
    // Create a new date object set to the last day of the given month and year
    var date = new Date(year, month, 0);
  
    // Set the date to the last Monday before the last day of the month
    while (date.getDay() !== 1) {
      date.setDate(date.getDate() - 1);
    }
  
    // Get the date (day of the month) of the last Monday
    var lastMondayDate = date.getDate();
  
    return lastMondayDate;
  }

const monthText = document.querySelector('.month'),
yearText = document.querySelector('.year'),
daysTxt = document.querySelector('.days');

var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

var currentDate = new Date(),
year = currentDate.getFullYear(),
month = currentDate.getMonth();

var monthState = 0;

const previousMonthBtn = document.querySelector('.previous-month-btn');
const nextMonthBtn = document.querySelector('.next-month-btn');

nextMonthBtn.addEventListener('click', function(){
    monthState += 1;

    monthText.innerHTML = months[(currentDate.getMonth() + monthState) % 12];
    
    if((currentDate.getMonth() + monthState) % 12 == 0){
        year += 1;
    }
    if(monthState == 12){
        monthState = 0;
    }
    yearText.innerHTML = year;
    month = currentDate.getMonth() + monthState;

    daysTxt.innerHTML = renderDays(year, month, monthState);
    console.log(monthState);

})

previousMonthBtn.addEventListener('click', function(){

    if(currentDate.getMonth() + monthState == 0){
        year -= 1;
    }
    if(monthState == 0){
        monthState = 12;
    }
    monthState -= 1;
    console.log(monthState);

    monthText.innerHTML = months[(currentDate.getMonth() + monthState) % 12];

    yearText.innerHTML = year;
    month = currentDate.getMonth() + monthState;

    daysTxt.innerHTML = renderDays(year, month, monthState);
})
const renderDays = (year, month, monthState) => {

    var checkWeek = 0;
    var dayStatus = 'inactive';
    var lastMonthStart = lastMondayOfMonth(month, year);

    var lastMonthEnd = new Date(year, currentDate.getMonth() + monthState, 0).getDate();
    var currentMonthEnd = new Date(year, currentDate.getMonth() + 1 + monthState, 0).getDate();
    var dayNo = lastMonthStart;
    // console.log(lastMonthStart)

    var code = "";
    for(var i=0; i<42; i++){

        if(checkWeek == 0){
            code += '<div class="days-line">'
        }
        if(dayNo > lastMonthEnd && dayStatus == 'inactive'){
            dayStatus = 'active';
            dayNo = 1;
        }
        if(dayNo > currentMonthEnd && dayStatus == 'active'){
            dayStatus = 'inactive';
            dayNo = 1;
        }

        if(dayStatus == 'active' && monthState == 0 && i == currentDate.getDate() + (lastMonthEnd - lastMonthStart)){
            code += `<p class="day today ${dayStatus}">${dayNo}</p>`;
        }else if(monthState == 0 && i %11 == 1 && dayStatus == 'active'){
            code += `<p class="day deadline ${dayStatus}">${dayNo}</p>`;
        }
        else{
            code += `<p class="day ${dayStatus}">${dayNo}</p>`;
        }
        
        dayNo += 1;
        checkWeek += 1;

        if(checkWeek == 7){
            code += '</div>';
            checkWeek = 0;
        }
    }
    return code;
}


const renderCalendar = (year, month) => {
    // getting last date of month
    // let lastDateOfMonth = new Date(year, currentDate.getMonth() + 1, 0).getDate();

    monthText.innerHTML = months[month];
    yearText.innerHTML = year;

    daysTxt.innerHTML = renderDays(year, month, monthState);
};
renderCalendar(year, month);

















// let outer = document.querySelector(".outer"),
// progressValue = document.querySelector(".progress-status");

// let progressStartValue = 0,    
// progressEndValue = 65,    
// speed = 20;

// let progress = setInterval(() => {
// progressStartValue++;

// progressValue.textContent = `${progressStartValue}%`
// outer.style.background = `conic-gradient(#283A5A ${progressStartValue * 3.6}deg, #f7f7f7 0deg)`

// if(progressStartValue == progressEndValue){
//     clearInterval(progress);
// }    
// }, speed);




// const CreateProjectButton = document.getElementById("create-project-btn");
const LogOutButton = document.getElementById("log-out-btn");
// const CreateProjectForm = document.getElementById("create-project-form");

// const ProjectContainerDiv = document.getElementById("project-container-div");
// const ModelContainerDiv = document.getElementById("model-container-div");
// const CloseModelButton = document.getElementById("model-close-btn");

// const OnLoad = async function () {
//   // get the user projects from the backend
//   try {
//     let response = await fetch(
//       "http://localhost/public/user/projects", 
//       {
//         withCredentials: true,
//         credentials: "include",
//         mode: "cors",
//         method: "GET",
//       }
//     );

//     // this is needed for the landing page
//     let obj = await response.json();
//     console.log(obj);

//     if (response.ok) {
//       if (obj.projects !== undefined) {
//         obj.projects.forEach((element) => {
//           createProjectDiv(element);
//         });
//       }
//     }
//    } catch (error) {
//     console.error(error);
//   }
// };

// function removeElementsFromProjectContainerDiv() {
//   ProjectContainerDiv.innerHTML = "";
// }

// // open the model on click
// CreateProjectButton.addEventListener("click", () => {
//   console.log("This is the create-project button click event");
//   ModelContainerDiv.classList.add("show-model");
// });

// CloseModelButton.addEventListener("click", () => {
//   console.log("This is the create-project button click event");
//   ModelContainerDiv.classList.remove("show-model");
//   removeElementsFromProjectContainerDiv();
//   OnLoad();
// });

// CreateProjectForm.addEventListener("submit", (event) => {
//   event.preventDefault();
//   const CreateProject = async function createProject() {
//     let formData = new FormData(CreateProjectForm);
//     let jsonFormData = JSON.stringify(Object.fromEntries(formData));
//     console.log(jsonFormData);

//     // have to perform validations on the form data
//     try {
//       let response = await fetch(
//         "http://localhost/public/user/projects",
//         {
//           withCredentials: true,
//           credentials: "include",
//           mode: "cors",
//           method: "POST",
//           body: jsonFormData,
//         }
//       );

//       let obj = await response.json();
//       console.log(obj);

//       if (response.ok) {
//         console.log(obj);
//         alert("New project is created");
//         // reset the input fields
//         CreateProjectForm.reset();
//         // window.replace('./landing.html');
//       } else {
//         // check the errors
//         alert("Project cannot be created");
//       }
//     } catch (error) {
//       console.error(error);
//     }
//   };
//   CreateProject();
// });

LogOutButton.addEventListener("click", () => {
    fetch("http://localhost/public/user/logout", {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "POST",
        })
        .then((response) => {
            if (response.ok) {
                window.location.replace("http://localhost/public/user/login");
                return;
            }
            if (!response.ok) {
                response.json();
            }
        })
        .then((data) => {
            if (data.message != undefined && data.message != undefined) {
                alert(data.message);
            } else {
                alert(data.message);
            }
        })
        .catch((error) => {
            console.error(error)
        });
});

// // takes a js object as an argument
// function createProjectDiv(obj) {
//   const Project = document.createElement("div");
//   Project.className = "project-card";

//   Project.innerHTML = `
//         <h3 style="width: 400px;">${obj.project_name}</h3>
//         <div style="height: 150px; background-color: bisque; width: 100%; margin: 10px;">
//                 An image goes here
//         </div>
//         <p style="width: 400px;"> Description : ${obj.description}</p>
//         <br>
//         <p style="width: 400px;"> Start Date : ${obj.start_on}</p>
//         <br>
//         <p style="width: 400px;"> End Date : ${obj.end_on}</p>
//         `;
//   Project.addEventListener('click', function() {
//       const PAGER_FOR_USER_PROJECTS = async function (obj) {
//       let url = "http://localhost/public/user/project?id=" + obj.id;
//       let response = await fetch(url, {
//             withCredentials: true,
//             credentials: "include",
//             mode: "cors",
//             method: "GET",
//         }
//       );
//       if (response.ok) {
//         let res = await response.json();
//         window.location.replace(res.url);
//       }
//     }
//     PAGER_FOR_USER_PROJECTS(obj);
//   });
//   ProjectContainerDiv.appendChild(Project);
// }
