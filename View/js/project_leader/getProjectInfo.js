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