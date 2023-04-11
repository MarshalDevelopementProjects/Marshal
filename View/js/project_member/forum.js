const notificationPopupBtn = document.querySelector('.notification-bell-btn')
const notificationPopup = document.querySelector('.notification-popup-container');
const notificationPopupCloseBtn = document.querySelector('.notification-popup-close-btn');
const notificationPopupContainer = document.querySelector('.notification-popup-container');
const container = document.querySelector('.container');

notificationPopupBtn.addEventListener('click', () => notificationPopup.classList.add('active'));
notificationPopupCloseBtn.addEventListener('click', () => notificationPopup.classList.remove('active'));
notificationPopup.addEventListener('click', () => notificationPopup.classList.remove('active'))

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

nextMonthBtn.addEventListener('click', function() {
    monthState += 1;

    monthText.innerHTML = months[(currentDate.getMonth() + monthState) % 12];

    if ((currentDate.getMonth() + monthState) % 12 == 0) {
        year += 1;
    }
    if (monthState == 12) {
        monthState = 0;
    }
    yearText.innerHTML = year;
    month = currentDate.getMonth() + monthState;

    daysTxt.innerHTML = renderDays(year, month, monthState);
    console.log(monthState);

})

previousMonthBtn.addEventListener('click', function() {

    if (currentDate.getMonth() + monthState == 0) {
        year -= 1;
    }
    if (monthState == 0) {
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
    for (var i = 0; i < 42; i++) {

        if (checkWeek == 0) {
            code += '<div class="days-line">'
        }
        if (dayNo > lastMonthEnd && dayStatus == 'inactive') {
            dayStatus = 'active';
            dayNo = 1;
        }
        if (dayNo > currentMonthEnd && dayStatus == 'active') {
            dayStatus = 'inactive';
            dayNo = 1;
        }

        if (dayStatus == 'active' && monthState == 0 && i == currentDate.getDate() + (lastMonthEnd - lastMonthStart)) {
            code += `<p class="day today ${dayStatus}">${dayNo}</p>`;
        } else if (monthState == 0 && i % 11 == 1 && dayStatus == 'active') {
            code += `<p class="day deadline ${dayStatus}">${dayNo}</p>`;
        } else {
            code += `<p class="day ${dayStatus}">${dayNo}</p>`;
        }

        dayNo += 1;
        checkWeek += 1;

        if (checkWeek == 7) {
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

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// WS connection

console.log(jsonData);
let projectID = jsonData.project_id;
let userData = jsonData.user_data;
let date = new Date();
// For today's date;
Date.prototype.today = function () {
    return ((this.getDate() < 10)?"0":"") + this.getDate() +"/"+(((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) +"/"+ this.getFullYear();
}

// For the time now
Date.prototype.timeNow = function () {
    return ((this.getHours() < 10)?"0":"") + this.getHours() +":"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes() +":"+ ((this.getSeconds() < 10)?"0":"") + this.getSeconds();
}

let projectMemberForumConnection = new WebSocket(`ws://localhost:8080/projects/forum?project=${projectID}`);

projectMemberForumConnection.onopen = (event) => {
    console.log(event.data);
}

projectMemberForumConnection.onclose = (event) => {
    console.log(event.data);
}

projectMemberForumConnection.onerror = (event) => {
    console.error(event.data);
}

projectMemberForumConnection.onmessage = (event) => {
    // A JSON STRING WILL BE SENT FROM THE SENDER PARSE IT AND TAKE THE DATA
    // Message data must be a json string of this form
    // {
    //      username: "USERNAME OF THE SENDER",
    //      profile_picture: "PATH TO THE PROFILE PICTURE OF THE SENDER",
    //      date_time: "DATE TIME STRING",
    //      message: "BODY MESSAGE "
    // }
    // TODO: ATTACH THE INCOMING DATA TO THE FORUM
    console.log(event.data);
    function onMessage(messageData) {
        let message_data = JSON.parse(messageData); // parse the incoming JSON encoded message
        console.log(message_data);
        /*if(message_data.status !== undefined) console.log(message_data.status);
        if(message_data.username !== undefined) console.log(message_data.username);
        if(message_data.profile_picture !== undefined) console.log(message_data.profile_picture);
        if(message_data.date_time!== undefined) console.log(message_data.date_time);
        if(message_data.message !== undefined)console.log(message_data.message);*/
    }
    onMessage(event.data);
}

let msgObj = {
    username:   userData.username,
    profile_picture: userData.profile_picture,
};

async function onStartUp() {
    // TODO: SEND A GET REQUEST TO THE
    let url = "http://localhost/public/project/member/project/forum/messages";
    try {
        let response = await fetch(url, {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "GET",
        });
        if (response.ok) {
            let data = response.json();
            // TODO: ATTACH THE MESSAGES TO THE FORUM
            if (data.length > 0) {
                data.forEach(
                    message => console.log(message)
                );
            } else {
                console.log("No messages to display")
            }
        }
    } catch (error) {
        console.error(error);
        projectMemberForumConnection.close();
    }
}

function closeConnection() {
    // TODO: CLOSE THE POP-UP OR DO SOMETHING ELSE
    projectMemberForumConnection.close();
}

// have to give a json string as the message to this function
// this message argument must be of an object of the form
// {
//      username: "USERNAME OF THE SENDER",
//      profile_picture: "PATH TO THE PROFILE PICTURE OF THE SENDER",
//      date_time: "DATE TIME STRING",
//      task_id: "If this is a task message need the task id"
//      group_id: "If this is a group message need the task id"
//      message: "BODY MESSAGE "
// }
async function sendMessages(msg) {
    // TODO: ATTACH THE MESSAGE TO THE MESSAGING FORUM

    // TODO: SEND THE MESSAGE
    msgObj.message = msg;
    msgObj.date_time = `${date.today()} ${date.timeNow()}`;
    projectMemberForumConnection.send(JSON.stringify(msgObj));
    console.log(msgObj);

    // TODO: SEND THE MESSAGE TO THE APPROPRIATE END POINT(ASYNC)
    let url = "http://localhost/public/project/member/project/forum/messages";
    let requestBody =  {
        message: msgObj.message
    };
    try {
        let response = await fetch(url, {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "POST",
            body: JSON.stringify(requestBody)
        });
        if (response.ok) {
            let data = response.json();
            console.log(data);
        }
    } catch (error) {
        console.error(error);
        projectMemberForumConnection.close();
    }
}