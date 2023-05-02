console.log(jsonData)

const notificationPopupBtn = document.querySelector('.notification-bell-btn')
const notificationPopup = document.querySelector('.notification-popup-container');
const notificationPopupCloseBtn = document.querySelector('.notification-popup-close-btn');
const notificationPopupContainer = document.querySelector('.notification-popup-container');
const container = document.querySelector('.container');

notificationPopupBtn.addEventListener('click', () => notificationPopup.classList.add('active'));
notificationPopupCloseBtn.addEventListener('click', () => notificationPopup.classList.remove('active'));
notificationPopup.addEventListener('click', () => notificationPopup.classList.remove('active'))


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

// get annoucement 
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