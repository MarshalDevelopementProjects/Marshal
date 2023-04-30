// console.log(jsonData)

// const toastContainer = document.querySelector('.toast-notification');
// const notificationCloseBtn = document.querySelector('.close-btn');
// var toastNotificationTimeout = 5000;

// setTimeout(() => {
//     toastContainer.classList.add('active');
// }, 1000);
// setTimeout(() => {
//     toastContainer.classList.remove('active');
// }, toastNotificationTimeout);

// // console.log(toastNotificationTimeout)

// notificationCloseBtn.addEventListener('click', () => toastContainer.classList.remove('active'));

/* --------------------added separate file notificationPopup--------------------------------*/ 
// const notificationPopupBtn = document.querySelector('.notification-bell-btn')
// const notificationPopup = document.querySelector('.notification-popup-container');
// const notificationPopupCloseBtn = document.querySelector('.notification-popup-close-btn');
// const notificationPopupContainer = document.querySelector('.notification-popup-container');
// const container = document.querySelector('.container');

// notificationPopupBtn.addEventListener('click', () => notificationPopup.classList.add('active'));
// notificationPopupCloseBtn.addEventListener('click', () => notificationPopup.classList.remove('active'));
// // notificationPopup.addEventListener('click', () => notificationPopup.classList.remove('active'))
/*---------------------------------------------------------------------------------------------------------- */


//$notifications = [];

// check notifications for this user
// const toastNotificationDetails = document.querySelector('.details'),
const projects = document.querySelector('.projects');

function getProjectsCode(projects){
    // get ongoing tasks to an array 
    var ongoingTasks = {};
    var members = {};
    
    projects.forEach(project => {
        ongoingTasks[project['id']] = [];
        members[project['id']] = [];

        // get tasks
        if(project.tasks.length){
            for(let i = 0; i<4; i++){
                if(project.tasks[i] == undefined){
                    ongoingTasks[project['id']].push(" ");
                }else{
                    ongoingTasks[project['id']].push(project.tasks[i]['task_name']);
                }
            }
        }else{
            ongoingTasks[project['id']].push("There is nothing onging. Create and pickup new one.");
            for(let i = 1; i<4; i++){
                ongoingTasks[project['id']].push(" ");
            }
        }

        // get member profile details
        for(let i = 0; i <5; i++){
            if(project.memberProfiles[i] == undefined){
                members[project['id']].push('src="/View/images/Picture1.png" style="display: none;"');
            }else{
                members[project['id']].push('src=' + project.memberProfiles[i]['profile_picture']);
            }
        }
    })

    // console.log(typeof(jsonData['projects'][0]['memberProfiles'][0]['profile_picture']))
    projectCardsCode = ""
    projects.forEach(project => {
        projectCardsCode += `<div class="project-card">
                                <a href="http://localhost/public/user/project?id=${project['id']}" class="clickable-project">
                                    <p class="project-field ">${project['field']}</p>
                                <h3 class="project-name">${project['project_name']}</h3>

                                <div class="tasks">
                                    <p>Ongoing Work</p>
                                    <ul class="task-list">
                                        <li>${ongoingTasks[project['id']][0]}</li>
                                        <li>${ongoingTasks[project['id']][1]}</li>
                                        <li>${ongoingTasks[project['id']][2]}</li>
                                        <li>${ongoingTasks[project['id']][3]}</li>
                                        
                                    </ul>
                                </div>
                                <p class="team">Team</p>
                                <div class="card-bottom">
                                    <div class="member-images">
                                        <img class="image first" ${members[project['id']][0]} alt="Picture1">
                                        <img class="image rest1" ${members[project['id']][1]} alt="Picture2">
                                        <img class="image rest2" ${members[project['id']][2]} alt="Picture3">
                                        <img class="image rest3" ${members[project['id']][3]} alt="Picture4">
                                        <img class="image rest4" ${members[project['id']][4]} alt="Picture5">
                                    </div>
                                    <!-- <button type="submit">Get Info</button> -->
                                    
                                </div>
                                </a>
                            </div>`
    })
    return projectCardsCode
}

// this onLoad function receive notifications and load page data
function onLoad(){
    // load project data

    let projectCardsCode = getProjectsCode(jsonData['projects'])

    projects.innerHTML = projectCardsCode

}

// Add profile picture
const profilePicture = document.querySelector('.profile-image');
profilePicture.src = jsonData['profile'];

// build search engine

const searchInput = document.querySelector('.search-box input');

function getProjectNames(projects){
    var projectNames = [];
    projects.forEach(project => {
        projectNames.push(project['project_name'])
    })
    return projectNames
}

function getMatchedProjectNames(keyword){
    let projects = getProjectNames(jsonData['projects'])
    return projects.filter(project => project.toLowerCase().startsWith(keyword.toLowerCase()))
}

function getMatchedProjects(keyword){
    let projectNames = getMatchedProjectNames(keyword)
    return jsonData['projects'].filter(project => projectNames.includes(project['project_name']))
}

searchInput.addEventListener('input', () =>{
    console.log(getMatchedProjects(searchInput.value))
    let code = getProjectsCode(getMatchedProjects(searchInput.value))

    projects.innerHTML = code
})


/*------------------------------------------------------*/ 
const Project = document.querySelector('.projects')
const middle_ = document.querySelector('.middle-icon');
const middle_2 = document.querySelector('.middle-icon2');

middle_.addEventListener('click', function(){
    setTimeout(function() {
        Project.classList.add('active');
      }, 1000); // wait 1 second (1000 milliseconds)   
});

middle_2.addEventListener('click', function(){
    setTimeout(function() {
        Project.classList.remove('active');
      }, 1000); // wait 1 second (1000 milliseconds)   
});
/*------------------------------------------------------*/ 
