console.log(jsonData);

// Set up
const ScheduleContainer = document.getElementById('schedule-container-div');
const CurrentProjectScheduleContainer = document.getElementById('current-schedule-container-div');
// const ClientContainerDiv = document.getElementById('current-schedule-container-div');
const ScheduleDataInputForum = document.getElementById('schedule-detail-input-form');
const userData = jsonData.user_data;
let allSchedules = jsonData.all_conference_details.reverse();
console.log(allSchedules)
let projectSchedules = jsonData.project_conference_details.reverse();
console.log(projectSchedules)
const clientsOfProject = jsonData.clients_of_the_project;

document.body.onload = async () => {await onLoad();};

ScheduleDataInputForum.addEventListener('submit', async (event) => {
    event.preventDefault();
    // TODO: get the data and fix the schedule
    let formObj = Object.fromEntries(new FormData(ScheduleDataInputForum));
     await createNewSchedule(formObj);
    ScheduleDataInputForum.reset();
});

async function onLoad() {
    appendSchedules(ScheduleContainer, allSchedules);
    appendSchedules(CurrentProjectScheduleContainer, projectSchedules);
    // appendClients(ClientContainerDiv, clientsOfProject);
}

async function createNewSchedule(scheduleObj) {
    // TODO: SEND THE MESSAGE TO THE APPROPRIATE END POINT(ASYNC)
    let url = "http://localhost/public/project/leader/conference/schedule";

    console.log(scheduleObj);

    try {
        let response = await fetch(url, {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "POST",
            body: JSON.stringify(scheduleObj)
        }).then(async response => {
            if (response.ok) {
                let data = await response.json();
                console.log(data);
                alert(data.message);
                window.location.reload();
            } else {
                let error = await response.json();
                alert(error.message);
                console.error(error);
            }
        });
    } catch (error) {
        console.error(error);
    }
}

function appendSchedules(parentContainerDiv, schedules) {
    console.log(schedules);
    if (schedules.length > 0) {
        schedules.forEach((schedule) => {
            appendSchedule(parentContainerDiv, schedule);
        });
    } else {
        let text = document.createElement('h2');
        text.setAttribute('style', 'margin-top: 15%; margin-left: 20px; color: #a39a99;');
        text.innerText = 'There are no scheduled conferences';
        parentContainerDiv.appendChild(text);
    }
}

function appendSchedule(parent_div, schedule) {
    console.log(schedule);
    if (schedule) {
        let scheduleDiv = document.createElement('div');
        scheduleDiv.setAttribute('class', 'schedule');

        let scheduleName = document.createElement('p');
        scheduleName.setAttribute('class', 'schedule-name');
        scheduleName.innerText = schedule.scheduled_name;

        let scheduleDescriptionDiv = document.createElement('div');
        scheduleDescriptionDiv.setAttribute('class', 'schedule-des');

        let scheduleDescription = document.createElement('p');
        scheduleDescription.setAttribute('class', 'schedule-description');
        scheduleDescription.innerText = schedule.scheduled_description;

        scheduleDescriptionDiv.appendChild(scheduleDescription);

        let scheduleDateTimeDiv = document.createElement('div');
        scheduleDateTimeDiv.setAttribute('class', 'schedule-date-time');

        let userDetailsDiv = document.createElement('div');
        userDetailsDiv.setAttribute('class', 'user-details');

        let userProfileDiv = document.createElement('div');
        userProfileDiv.setAttribute('class', 'user-profile');

        let userProfileImg = document.createElement('img');
        userProfileImg.setAttribute('src', schedule.caller_dp);

        let usernameDiv = document.createElement('div');
        usernameDiv.setAttribute('class', 'user-name');

        let withP = document.createElement('p');
        withP.innerText = "With :";

        let usernameP = document.createElement('p');
        usernameP.setAttribute('id', 'user-name');
        usernameP.innerText = schedule.caller_username;

        usernameDiv.appendChild(withP);
        usernameDiv.appendChild(usernameP);
        userProfileDiv.appendChild(userProfileImg);

        userDetailsDiv.appendChild(userProfileDiv);
        userDetailsDiv.appendChild(usernameDiv);

        let dateTimeContainerDiv = document.createElement('div');
        dateTimeContainerDiv.setAttribute('class', 'date-time-container');

        let dateP = document.createElement('p');
        dateP.setAttribute('id', 'date');
        dateP.innerText = "On :" + schedule.scheduled_date;

        let timeP = document.createElement('p');
        timeP.setAttribute('id', 'time');
        timeP.innerText = "At :" + schedule.scheduled_time;

        let statusP = document.createElement('p');
        statusP.setAttribute('id', 'status');

        let statusI = document.createElement('i');
        statusI.setAttribute('class', 'fa-solid fa-circle');

        statusP.appendChild(statusI);

        if (schedule.meeting_status === "OVERDUE") {
            statusI.setAttribute('style', 'color: red');
        } else if (schedule.meeting_status === "PENDING") {
            statusI.setAttribute('style', 'color: green');

        } else if(schedule.meeting_status === "CANCELLED") {
            statusI.setAttribute('style', 'color: black');
        } else {
            statusI.setAttribute('style', 'color: grey');
        }


        dateTimeContainerDiv.appendChild(dateP);
        dateTimeContainerDiv.appendChild(timeP);
        dateTimeContainerDiv.appendChild(statusP);

        scheduleDateTimeDiv.appendChild(userDetailsDiv);
        scheduleDateTimeDiv.appendChild(dateTimeContainerDiv);

        scheduleDiv.appendChild(scheduleName);
        scheduleDiv.appendChild(scheduleDescriptionDiv);

        scheduleDiv.appendChild(scheduleDateTimeDiv);

        if (schedule.meeting_status === "PENDING") {
            let link = document.createElement('a');
            link.setAttribute('style', 'text-decoration: none; color: #333;');
            link.setAttribute('href', `http://localhost/public/project/leader/conference?conf_id=${schedule.conf_id}`); // TODO :: SET THE LINK LATER
            link.appendChild(scheduleDiv);
            parent_div.appendChild(link);
        } else {
            parent_div.appendChild(scheduleDiv);
        }
    }
}

function appendClients(parentDiv, clients) {
    if (clients.length > 0) {
        clients.forEach((client) => {
            appendClient(parentDiv, client);
        });
    } else {
        let text = document.createElement('h6');
        text.innerText = 'There are no clients in this project';
        parentDiv.appendChild(text);
    }
}

function appendClient(parent_div, client) {
    if (client && client.username && client.profile_picture) {

        let clientCard = document.createElement('div');
        clientCard.setAttribute('class', 'client-card');

        let profilePictureDiv = document.createElement('div');
        profilePictureDiv.setAttribute('class', 'profile-image');

        clientCard.appendChild(profilePictureDiv);

        let profileImage = document.createElement('img');
        profileImage.setAttribute('src', client.profile_picture);

        profilePictureDiv.appendChild(profileImage);

        let clientInfoDiv = document.createElement('div');
        clientInfoDiv.setAttribute('class', 'client-info');

        clientCard.appendChild(clientInfoDiv);

        let clientUsername = document.createElement('h6');
        clientUsername.innerText = client.username;

        clientInfoDiv.appendChild(clientUsername);

        parent_div.appendChild(clientCard);
    } else {
        console.error('empty fields given');
    }
}