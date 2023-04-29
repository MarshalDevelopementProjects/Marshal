console.log(jsonData)

const notificationPopupBtn = document.querySelector('.notification-bell-btn')
const notificationPopup = document.querySelector('.notification-popup-container');
const notificationPopupCloseBtn = document.querySelector('.notification-popup-close-btn');
const notificationPopupContainer = document.querySelector('.notification-popup-container');
const container = document.querySelector('.container');

notificationPopupBtn.addEventListener('click', () => notificationPopup.classList.add('active'));
notificationPopupCloseBtn.addEventListener('click', () => notificationPopup.classList.remove('active'));
notificationPopup.addEventListener('click', () => notificationPopup.classList.remove('active'))





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

function notMemberWarningPopup(event) {
    var popup = document.querySelector(".not-member-warning");

    popup.style.display = "flex"; // show the popup

    console.log(event.clientX, event.clientY)

    let warningCancelBtn = document.querySelector('.not-member-warning button')
    warningCancelBtn.addEventListener('click', () => {
        popup.style.display = "none"; // hide the popup after 5 seconds
    })

    setTimeout(function() {
      popup.style.display = "none"; // hide the popup after 5 seconds
    }, 5000);
  }

const groups = document.querySelector('.groups');
var groupsCode = "";


if(jsonData['groups']){
    jsonData['groups'].forEach(group => {

        if(!group['hasAccess']){
            groupsCode += `<div class="not-member-group" onclick="notMemberWarningPopup(event)"><p class = "group">${group['group_name']}</p></div>`
        }else{
            groupsCode += `<a href="http://localhost/public/projectmember/group?id=${group['id']}"><p class="group">${group['group_name']}</p></a>`
        }
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

const announcementForum = document.querySelector('.feedback-form');

function setProjectAnnouncements(){

  fetch("http://localhost/public/projectmember/announcement", {
    withCredentials: true,
      credentials: "include",
      mode: "cors",
      method: "GET"
  })
  .then(response => response.json())
  .then(data => {
        let announcementForumCode = ``
        console.log(data['message'])
        data['message'] = data['message'].reverse()
        data['message'].forEach(announcement => {
          announcementForumCode += `<div class="announcement">
                                      <img src="${announcement['profile']}" alt="">
                                      <div class="announcement-details">
                                          <div class="announcement-detail-heading">
                                              <p class="announcement-heading-line">${announcement['heading']}</p>
                                              <p class="announcement-sent-time"><span style="color: #031738; font-size: 13px; font-weight: 550">${announcement['senderType']}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;${announcement['stamp']}</p>
                                          </div>
                                          <p class="announcement-detail-message">${announcement['msg']}</p>
                                      </div>
                                  </div>`
        })
        announcementForum.innerHTML = announcementForumCode
  })
  .catch((error) => {
      console.error(error)
  })

}

setProjectAnnouncements()