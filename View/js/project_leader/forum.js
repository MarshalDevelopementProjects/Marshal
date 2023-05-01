

const MessageContainerDiv = document.getElementById('messages-container-div');
const MessageInputForm = document.getElementById('message-input-form');
console.log(jsonData);
let projectID = jsonData.project_id;
let userData = jsonData.user_data;
let date = new Date();

document.body.onload = async () => { await onLoad(); };

// For today's date;
Date.prototype.today = function () {
    return ((this.getDate() < 10)?"0":"") + this.getDate() +"/"+(((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) +"/"+ this.getFullYear();
}

// For the time now
Date.prototype.timeNow = function () {
    return ((this.getHours() < 10)?"0":"") + this.getHours() +":"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes() +":"+ ((this.getSeconds() < 10)?"0":"") + this.getSeconds();
}

let projectLeaderForumConnection = new WebSocket(`ws://localhost:8080/projects/forum?project=${projectID}`);

projectLeaderForumConnection.onopen = (event) => {
    console.log(event.data);
}

projectLeaderForumConnection.onclose = (event) => {
    console.log(event.data);
}

projectLeaderForumConnection.onerror = (event) => {
    console.error(event.data);
}

projectLeaderForumConnection.onmessage = (event) => {
    // A JSON STRING WILL BE SENT FROM THE SENDER PARSE IT AND TAKE THE DATA
    // Message data must be a json string of this form
    // {
    //      username: "USERNAME OF THE SENDER",
    //      profile_picture: "PATH TO THE PROFILE PICTURE OF THE SENDER",
    //      date_time: "DATE TIME STRING",
    //      message: "BODY MESSAGE "
    // }
    console.log(event.data);
    // TODO: ATTACH THE INCOMING DATA TO THE FORUM
    onMessage(event.data);
}

function onMessage(messageData) {
    let message_data = JSON.parse(messageData); // parse the incoming JSON encoded message
    console.log(message_data);
    /*if(message_data.status !== undefined) console.log(message_data.status);
    if(message_data.sender_username !== undefined) console.log(message_data.username);
    if(message_data.sender_profile_picture !== undefined) console.log(message_data.profile_picture);
    if(message_data.stamp!== undefined) console.log(message_data.date_time);
    if(message_data.msg !== undefined)console.log(message_data.message);*/
    if(message_data.sender_username !== undefined)
        appendMessage('IN', MessageContainerDiv, message_data);
}

// have to get the message data from the backend and then load them to the
// chat forum use the GET end points
async function onLoad() {
   await createForumMessage();
   createProjectMemberList(jsonData);
}

async function createForumMessage() {
    // TODO: GET ALL THE  MESSAGES FROM THE APPROPRIATE END POINT(ASYNC)
    let url = "http://localhost/public/project/leader/project/forum/messages";
    try {
        let response = await fetch(url, {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "GET",
        });

        if (response.ok) {
            let data = await response.json();
            console.log(data);
            if (data.messages.length > 0) {
                data.messages.forEach(
                    message => {
                        // TODO: ATTACH THE MESSAGES TO THE FORUM
                        // check whether the messages are incoming messages or outgoing messages
                        if (message.sender_username !== userData.username) {
                            appendMessage('IN', MessageContainerDiv, message);
                        } else {
                            appendMessage('OUT', MessageContainerDiv, message);
                        }
                    }
                );
            }
        }
    } catch (error) {
        console.error(error);
        projectLeaderForumConnection.close();
    }
}

function closeConnection() {
    // TODO: CLOSE THE POP-UP OR DO SOMETHING ELSE
    projectLeaderForumConnection.close();
}

// have to give a json string as the message to this function
// this message argument must be of an object of the form
// {
//      username: "USERNAME OF THE SENDER",
//      profile_picture: "PATH TO THE PROFILE PICTURE OF THE SENDER",
//      data_time: "DATE TIME STRING",
//      task_id: "If this is a task message need the task id"
//      group_id: "If this is a group message need the task id"
//      message: "BODY MESSAGE "
// }

MessageInputForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    let formObj = Object.fromEntries(new FormData(MessageInputForm));
    if (formObj.message !=="") {
        await sendMessages(formObj.message);
    }
    MessageInputForm.reset();
});

async function sendMessages(msg) {

    // create the message object
    let msgObj = {
        sender_username:   userData.username,
        sender_profile_picture: userData.profile_picture,
    };
    msgObj.msg = msg;
    msgObj.stamp = `${date.today()} ${date.timeNow()}`;

    // TODO: SEND THE MESSAGE
    projectLeaderForumConnection.send(JSON.stringify(msgObj));

    // TODO: ATTACH THE MESSAGE TO THE MESSAGING FORUM
    appendMessage('OUT', MessageContainerDiv, msgObj);

    // TODO: SEND THE MESSAGE TO THE APPROPRIATE END POINT(ASYNC)
    let url = "http://localhost/public/project/leader/project/forum/messages";
    let requestBody = {
        message: msgObj.msg
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
        projectLeaderForumConnection.close();
    }
}

function appendMessage(type, parent_div, message) {

    let message_div = document.createElement('div'); // message

    if (type === 'OUT') {
        message_div.setAttribute('class', 'outgoing-message');

        let date_time = document.createElement('p'); // date time paragraph tag
        date_time.setAttribute('class', 'outgoing-date-time');
        date_time.innerText = message.stamp;

        let message_content = document.createElement('p'); // message content
        message_content.setAttribute('class', 'outgoing-messages');
        message_content.innerText = message.msg;

        message_div.appendChild(message_content);
        message_div.appendChild(date_time);

    } else if (type === 'IN') {
        message_div.setAttribute('class', 'incomming-message');

        let sender_username = document.createElement('h5'); // sender user name heading
        sender_username.innerText = message.sender_username;

        let sender_profile_picture = document.createElement('img'); // sender profile picture img tag
        sender_profile_picture.src = message.sender_profile_picture;

        let inner_message_div = document.createElement('div'); // message
        inner_message_div.setAttribute('class', 'incomming-new-message');

        let date_time = document.createElement('p'); // date time paragraph tag
        date_time.setAttribute('class', 'incomming-time'); // date time paragraph tag
        date_time.innerText = message.stamp;

        let message_content = document.createElement('p'); // message content
        message_content.setAttribute('class', 'incomming-messages');
        message_content.innerText = message.msg;

        // adding elements
        inner_message_div.appendChild(message_content);
        inner_message_div.appendChild(date_time);
        message_div.appendChild(sender_profile_picture);
        message_div.appendChild(inner_message_div);

    } else {
        console.error('NOT A VALID MESSAGE TYPE');
        message_div = undefined;
        return;
    }

    parent_div.insertAdjacentElement("afterbegin", message_div);
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
console.log(jsonData);
const ProjectLeaderListDiv = document.getElementById('project-leaders-list-container-div');
const ProjectMemberListDiv = document.getElementById('project-members-list-container-div');

// Adding project members to the list
function createProjectMemberList(args) {
    if (args.members !== undefined) {
        args.members.forEach((member) => {
            console.log(member);
            if (member.role === 'LEADER') {
                appendProjectMember(ProjectLeaderListDiv, member);
            } else {
                appendProjectMember(ProjectMemberListDiv, member);
            }
        });
    } else {
        console.error('JSON Data did not return the member data');
    }
}

function appendProjectMember(parent_div, member_details) {
    if (member_details !== undefined) {

        let memberCard = document.createElement('div');
        memberCard.setAttribute('class', 'member-card');

        let profilePictureDiv = document.createElement('div');
        profilePictureDiv.setAttribute('class', 'profile-image');

        memberCard.appendChild(profilePictureDiv);

        let profileImage = document.createElement('img');
        profileImage.setAttribute('src', member_details.profile_picture);

        profilePictureDiv.appendChild(profileImage);

        let statusIcon = document.createElement('i');
        statusIcon.setAttribute('class', 'fa fa-circle');
        statusIcon.setAttribute('aria-hidden', 'true'); // need to ask about this

        profilePictureDiv.appendChild(profileImage);

        if (member_details.state === "ONLINE") {
            statusIcon.setAttribute('style', 'color: green');
        } else {
            statusIcon.setAttribute('style', 'color: red');
        }

        profilePictureDiv.appendChild(statusIcon);

        let memberInfoDiv = document.createElement('div');
        memberInfoDiv.setAttribute('class', 'member-info');

        memberCard.appendChild(memberInfoDiv);

        let memberUsername = document.createElement('h6');
        memberUsername.innerText = member_details.username;

        memberInfoDiv.appendChild(memberUsername);

        let memberStatus = document.createElement('p');
        memberStatus.innerText = member_details.status;

        memberInfoDiv.appendChild(memberStatus);

        // parent_div.appendChild(memberDiv);
        parent_div.appendChild(memberCard);

    } else {
        console.error('empty fields given');
    }
}