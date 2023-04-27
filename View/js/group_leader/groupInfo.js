console.log(jsonData)

const notificationPopupBtn = document.querySelector('.notification-bell-btn')
const notificationPopup = document.querySelector('.notification-popup-container');
const notificationPopupCloseBtn = document.querySelector('.notification-popup-close-btn');
const notificationPopupContainer = document.querySelector('.notification-popup-container');
const container = document.querySelector('.container');

notificationPopupBtn.addEventListener('click', () => notificationPopup.classList.add('active'));
notificationPopupCloseBtn.addEventListener('click', () => notificationPopup.classList.remove('active'));
notificationPopup.addEventListener('click', () => notificationPopup.classList.remove('active'))







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


// const createGroupPopup = document.querySelector('.create-group-container'),
// createGroupBtn = document.querySelector('#create-group-btn'),
// createGroupCancelBtn = document.querySelector('#cancel-create-group');

// createGroupBtn.addEventListener('click', () => {
//   createGroupPopup.classList.add('active');
// })
// createGroupCancelBtn.addEventListener('click', () => {
//   createGroupPopup.classList.remove('active')
// })






// set announcement settings

const showSendAnnouncementForm = document.querySelector('.group-announcement-area .show-send-announcement-form');
const feedbackForm = document.querySelector('.group-announcement-area .feedback-form');
const groupAnnouncementsForm = document.querySelector('.group-announcements-form');
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

function setGroupAnnouncements(){

  fetch("http://localhost/public/groupmember/announcement", {
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

setGroupAnnouncements()

// send announcement
const sendAnnouncementBtn = document.querySelector('#feedback-message-btn');
const announcementForm = document.querySelector('.group-announcements-form');

sendAnnouncementBtn.addEventListener('click', (event)=>{
    event.preventDefault();

    if(Object.fromEntries(new FormData(announcementForm)).announcementHeading && Object.fromEntries(new FormData(announcementForm)).announcementMessage){
      fetch("http://localhost/public/groupleader/announcement", {
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
            setGroupAnnouncements()
      })
      .catch((error) => {
          console.error(error)
      })
    }
    
})


// change project announcement and client feedback forums

var groupAnnouncements = true

const groupAnnouncementArea = document.querySelector(".group-announcement-area");
const leaderFeedbackArea = document.querySelector(".leader-feedback-area");
const groupAnnouncement = document.querySelector("#group-announcements");
const leaderFeedback = document.querySelector("#leader-feedback");

groupAnnouncement.addEventListener('click', () => {
  if(!groupAnnouncements){
    leaderFeedbackArea.classList.remove('active')
    leaderFeedback.classList.remove('active')
    groupAnnouncementArea.classList.remove('active')
    groupAnnouncement.classList.remove('active')

    groupAnnouncements = true
  }
})
leaderFeedback.addEventListener('click', () => {
  if(groupAnnouncements){
    leaderFeedbackArea.classList.add('active')
    leaderFeedback.classList.add('active')
    groupAnnouncementArea.classList.add('active')
    groupAnnouncement.classList.add('active')

    groupAnnouncements = false
  }
})


// set group details

const groupName = document.getElementById('groupIdentifier'),
  groupDescription = document.getElementById('group-description'),
  groupStartDate = document.getElementById('start-date'),
  groupEndDate = document.getElementById('end-date')

groupName.innerHTML = ""
groupName.innerHTML = `#Group : </span> ${jsonData['groupDetails']['name']} <span>${jsonData['groupDetails']['project_name']}</span>`

groupDescription.innerText = jsonData['groupDetails']['description']
groupStartDate.innerText = jsonData['groupDetails']['start_date']
groupEndDate.innerText = jsonData['groupDetails']['end_date']



// set profile picture

const profileImage = document.querySelector('.profile-image')

profileImage.src = jsonData['userDetails']

// set member list

const groupLeaderCard = document.getElementById('group-leader'),
  groupMemberCards = document.querySelector('.group-leaders')

groupLeaderCard.innerHTML = ` <div class="profile-image">
                                  <img src="${jsonData['groupLeader'][0]['profile_picture']}" alt="">
                                  <i class="fa fa-circle" aria-hidden="true"></i>
                              </div>
                              <div class="member-info">
                                  <h6>${jsonData['groupLeader'][0]['first_name']}  ${jsonData['groupLeader'][0]['last_name']}</h6>
                                  <p>${jsonData['groupLeader'][0]['position']}</p>
                              </div>`

let gMemberCardsCode = ""
jsonData['groupMembers'].forEach(member => {
  gMemberCardsCode += `<div class="member-card">
                        <div class="profile-image">
                            <img src="${member['profile_picture']}" alt="">
                            <i class="fa fa-circle" aria-hidden="true"></i>
                        </div>
                        <div class="member-info">
                            <h6>${member['first_name']}  ${member['last_name']}</h6>
                            <p>${member['position']}</p>
                        </div>
                      </div>`
})

groupMemberCards.innerHTML = gMemberCardsCode