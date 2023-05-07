console.log(jsonData)

const notificationPopupBtn = document.querySelector('.notification-bell-btn')
const notificationPopup = document.querySelector('.notification-popup-container');
const notificationPopupCloseBtn = document.querySelector('.notification-popup-close-btn');
const notificationPopupContainer = document.querySelector('.notification-popup-container');
const container = document.querySelector('.container');

notificationPopupBtn.addEventListener('click', () => notificationPopup.classList.add('active'));
notificationPopupCloseBtn.addEventListener('click', () => notificationPopup.classList.remove('active'));
notificationPopup.addEventListener('click', () => notificationPopup.classList.remove('active'))





// set the groups

const groups = document.querySelector('.groups');
var groupsCode = "";

if(jsonData['groups']){
    let createdGroups = jsonData['groups']
    createdGroups.reverse()

    createdGroups.forEach(group => {
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

/*// set member list
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
projectMembersCards.innerHTML = projectMemberCode;*/


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
                invitePopup.classList.remove('active')
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

// Add profile picture
const profilePicture = document.querySelector('.profile-image');
profilePicture.src = jsonData['profile'];


// set announcement settings

const showSendAnnouncementForm = document.querySelector('.project-announcement-area .show-send-announcement-form');
const feedbackForm = document.querySelector('.project-announcement-area .feedback-form');
const groupAnnouncementsForm = document.querySelector('.project-announcements-form');
const announcementSendForumDown = document.getElementById('announcement-down');

announcementSendForumDown.addEventListener('click', () => {
  showSendAnnouncementForm.classList.remove('active');
  feedbackForm.classList.remove('active');
  groupAnnouncementsForm.classList.remove('active');
})

showSendAnnouncementForm.addEventListener('click', () => {
  showSendAnnouncementForm.classList.add('active');
  feedbackForm.classList.add('active');
  groupAnnouncementsForm.classList.add('active');
})



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

// send announcement
const sendAnnouncementBtn = document.querySelector('#feedback-message-btn');
const announcementForm = document.querySelector('.project-announcements-form');

sendAnnouncementBtn.addEventListener('click', (event)=>{
    event.preventDefault();

    if(Object.fromEntries(new FormData(announcementForm)).announcementHeading && Object.fromEntries(new FormData(announcementForm)).announcementMessage){
      fetch("http://localhost/public/projectleader/announcement", {
          withCredentials: true,
          credentials: "include",
          mode: "cors",
          method: "POST",
          body: JSON.stringify({
              "announcementHeading" : Object.fromEntries(new FormData(announcementForm)).announcementHeading,
              "announcementMessage" : Object.fromEntries(new FormData(announcementForm)).announcementMessage
          })
      })
      .then(response => response.json())
      .then(data => {
            console.log(data)
            announcementForm.reset()
            setProjectAnnouncements()
      })
      .catch((error) => {
          console.error(error)
      })
    }
    
})


// change project announcement and client feedback forums

var groupAnnouncements = true

const groupAnnouncementArea = document.querySelector(".project-announcement-area");
const leaderFeedbackArea = document.querySelector(".client-feedback-area");
const grouptAnnouncement = document.querySelector("#project-announcements");
const leaderFeedback = document.querySelector("#client-feedback");

grouptAnnouncement.addEventListener('click', () => {
  if(!groupAnnouncements){
    leaderFeedbackArea.classList.remove('active')
    leaderFeedback.classList.remove('active')
    groupAnnouncementArea.classList.remove('active')
    grouptAnnouncement.classList.remove('active')

    groupAnnouncements = true
  }
})
leaderFeedback.addEventListener('click', () => {
  if(groupAnnouncements){
    leaderFeedbackArea.classList.add('active')
    leaderFeedback.classList.add('active')
    groupAnnouncementArea.classList.add('active')
    grouptAnnouncement.classList.add('active')

    groupAnnouncements = false
  }
})


// send message to client
// const clientFeedbackForm = document.getElementById('client-feedback-form');
// const clientFeedbackSendBtn = document.getElementById('feedback-message-btn');

// clientFeedbackSendBtn.addEventListener('submit', (event) => {
//   // console.log(Object.fromEntries(new FormData(clientFeedbackForm)).feedbackMessage)
//   fetch("http://localhost/public/projectleader/clientfeedback", {
//       withCredentials: true,
//       credentials: "include",
//       mode: "cors",
//       method: "POST",
//       body: JSON.stringify({
//           "feedbackMessage" : Object.fromEntries(new FormData(feedbackForm)).feedbackMessage
//       })
//   })
//   .then(response => response.json())
//   .then(data => {
//     console.log(data)
//   })
//   .catch((error) => {
//       console.error(error)
//   })
// })


const archivedBtn = document.querySelector(".archived"),
      confirmArchivePopup = document.querySelector(".confirm-archive-popup"),
      rightPanel = document.querySelector(".right-panel"),
      archiveCancelBtn = document.querySelector(".cancel-btn"),
      projectInfoBtn = document.querySelector(".project-info"),
      confirmArchiveBtn = document.querySelector('.archive-btn');

archivedBtn.addEventListener("click", () => {
    confirmArchivePopup.classList.add("active")
    rightPanel.classList.add("archive");
    projectInfoBtn.classList.remove("active")
    archivedBtn.classList.add("active")
});

archiveCancelBtn.addEventListener("click", () => {
    confirmArchivePopup.classList.remove("active")
    rightPanel.classList.remove("archive");
    projectInfoBtn.classList.add("active");
    archivedBtn.classList.remove("active");
});

confirmArchiveBtn.addEventListener("click", function(){
  
    fetch("http://localhost/public/user/archiveproject", {
      withCredentials: true,
        credentials: "include",
        mode: "cors",
        method: "GET"
    })
    .then(response => response.json())
    .then(data => {
          console.log(data)
          location.replace("http://localhost/public/user/dashboard")
    })
    .catch((error) => {
        console.error(error)
    })
})
