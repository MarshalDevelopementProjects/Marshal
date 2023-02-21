console.log(jsonData)

const notificationPopupBtn = document.querySelector('.notification-bell-btn')
const notificationPopup = document.querySelector('.notification-popup-container');
const notificationPopupCloseBtn = document.querySelector('.notification-popup-close-btn');
const notificationPopupContainer = document.querySelector('.notification-popup-container');
const container = document.querySelector('.container');

notificationPopupBtn.addEventListener('click', () => notificationPopup.classList.add('active'));
notificationPopupCloseBtn.addEventListener('click', () => notificationPopup.classList.remove('active'));
notificationPopup.addEventListener('click', () => notificationPopup.classList.remove('active'))


const navbarNewProjectBtn = document.querySelector('.navbar-new-project');
const createProjectPopup = document.querySelector('.create-project-container');
const createProjectCloseBtn = document.querySelector('.create-project-popup-close');

navbarNewProjectBtn.addEventListener('click', () => createProjectPopup.classList.add('active'));
createProjectCloseBtn.addEventListener('click', () => createProjectPopup.classList.remove('active'));




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


// set the groups

const groups = document.querySelector('.groups');
var groupsCode = "";

if(jsonData['groups']){
    jsonData['groups'].forEach(group => {
        groupsCode += `<a href="http://localhost/public/projectmember/group?id=${group['id']}"><p class="group">${group['group_name']}</p></a>`
    })
}

groups.innerHTML = groupsCode


// project details

var projectDataCode = `<div class="project-info-top">
                        <h2>${jsonData['projectData']['project_name']}</h2>
                        <p id="invite-member-btn"><i class="fa fa-user-plus" aria-hidden="true"></i> Invite member</p>
                        </div>
                        <p>${jsonData['projectData']['description']}</p>

                        <div class="progress-bar">
                        <p id="start-date">${jsonData['projectData']['start_on'].split(' ')[0]}</p>
                        <div class="bar"><div class="progress"></div></div>
                        <p id="end-date">${jsonData['projectData']['end_on'].split(' ')[0]}</p>
                        </div>`

const projectData = document.querySelector('.project-details');
projectData.innerHTML = projectDataCode;

// set member list
const projectLeaderCard = document.querySelector('#projectLeaderCard');
const groupLeadersCards = document.querySelector('.group-leaders');
const projectMembersCards = document.querySelector('.project-members');

projectLeaderCard.innerHTML = `<div class="profile-image">
                                    <img src="${jsonData['projectLeader'][0]['profile_picture']}" alt="">
                                    <i class="fa fa-circle" aria-hidden="true"></i>
                                </div>
                                <div class="member-info">
                                    <h6>${jsonData['projectLeader'][0]['first_name']}  ${jsonData['projectLeader'][0]['last_name']}</h6>
                                    <p>${jsonData['projectLeader'][0]['position']}</p>
                                </div>`;

    var groupLeaderCode = "";

if(jsonData['groupLeaders']){
    jsonData['groupLeaders'].forEach(groupLeader => {
        groupLeaderCode += `<div class="member-card">
                                <div class="profile-image">
                                    <img src="${groupLeader['profile_picture']}" alt="">
                                    <i class="fa fa-circle" aria-hidden="true"></i>
                                </div>
                                <div class="member-info">
                                    <h6>${groupLeader['first_name']}  ${groupLeader['last_name']}</h6>
                                    <p>${groupLeader['position']}</p>
                                </div>
                            </div>`
    })
}
groupLeadersCards.innerHTML = groupLeaderCode;

var projectMemberCode = "";

if(jsonData['projectMembers']){
    jsonData['projectMembers'].forEach(projectMember => {
        projectMemberCode += `<div class="member-card">
                                <div class="profile-image">
                                    <img src="${projectMember['profile_picture']}" alt="">
                                    <i class="fa fa-circle" aria-hidden="true"></i>
                                </div>
                                <div class="member-info">
                                    <h6>${projectMember['first_name']}  ${projectMember['last_name']}</h6>
                                    <p>${projectMember['position']}</p>
                                </div>
                            </div>`
    })

}
projectMembersCards.innerHTML = projectMemberCode;


const invitationSendBtn = document.getElementById('inviteBtn'),
inviteMemberBtn = document.getElementById('invite-member-btn'),
invitePopup = document.querySelector('.invite-popup'),
closeBtn = document.querySelector('#closeBtn');

inviteMemberBtn.addEventListener('click', () => {
  invitePopup.classList.add('active')
})
closeBtn.addEventListener('click', () => invitePopup.classList.remove('active'))

invitationSendBtn.addEventListener('click', function () {
    let input = document.querySelector('input[name="username"]');
    // console.log(userEnteredData)
    if(input.value){
        fetch(
            "http://localhost/public/projectleader/invite",
            {
              withCredentials: true,
              credentials: "include",
              mode: "cors",
              method: "POST",
              body: input.value,
              headers: {
                'Content-Type': 'application/json',
              }        
            }
          )
          .then(response => response.json())
            .then(data => {
                console.log(JSON.stringify(data));
            })
          .catch(function(error){console.log(error)})
    }else{
      invitePopup.classList.remove('active')
    }
})


const createGroupPopup = document.querySelector('.create-group-container'),
createGroupBtn = document.querySelector('#create-group-btn'),
createGroupCancelBtn = document.querySelector('#cancel-create-group');

createGroupBtn.addEventListener('click', () => {
  createGroupPopup.classList.add('active');
})
createGroupCancelBtn.addEventListener('click', () => {
  createGroupPopup.classList.remove('active')
})