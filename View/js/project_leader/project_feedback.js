const MessageContainerDiv = document.getElementById('messages-container-div');
const MessageInputForm = document.getElementById('message-input-form');
console.log(jsonData);
let projectID = jsonData.project_id;
let userData = jsonData.user_data;
let date = new Date();

// document.body.onload = async () => { await onSlideLoad();};

// For today's date;
Date.prototype.today = function () {
    return ((this.getDate() < 10)?"0":"") + this.getDate() +"/"+(((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) +"/"+ this.getFullYear();
}

// For the time now
Date.prototype.timeNow = function () {
    return ((this.getHours() < 10)?"0":"") + this.getHours() +":"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes() +":"+ ((this.getSeconds() < 10)?"0":"") + this.getSeconds();
}

let projectLeaderFeedbackForumConnection = new WebSocket(`ws://localhost:8080/projects/feedback?project=${projectID}`);

projectLeaderFeedbackForumConnection.onopen = (event) => {
    console.log(event.data);
}

projectLeaderFeedbackForumConnection.onclose = (event) => {
    console.log(event.data);
}

projectLeaderFeedbackForumConnection.onerror = (event) => {
    console.error(event.data);
}

projectLeaderFeedbackForumConnection.onmessage = (event) => {
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
        if(message_data.sender_username !== undefined)
            appendMessage('IN', MessageContainerDiv, message_data);
    }
    onMessage(event.data);
}

let msgObj = {
    username:   userData.username,
    profile_picture: userData.profile_picture,
};

// have to get the message data from the backend and then load them to the
// chat forum use the GET end points
async function onSlideLoad() {
    // TODO: GET ALL THE  MESSAGES FROM THE APPROPRIATE END POINT(ASYNC)
    let url = "http://localhost/public/project/leader/project/feedback/messages";
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
            } else {
                console.log("No messages to display")
            }
        }
    } catch (error) {
        console.error(error);
        projectLeaderFeedbackForumConnection.close();
    }
}

function closeConnection() {
    // TODO: CLOSE THE POP-UP OR DO SOMETHING ELSE
    projectLeaderFeedbackForumConnection.close();
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
    // TODO: ATTACH THE MESSAGE TO THE MESSAGING FORUM

    msgObj.message = msg;
    msgObj.date_time = `${date.today()} ${date.timeNow()}`;
    // TODO: SEND THE MESSAGE
    projectLeaderFeedbackForumConnection.send(JSON.stringify(msgObj));
    appendMessage('OUT', MessageContainerDiv, message);
    console.log(msgObj);

    // TODO: SEND THE MESSAGE TO THE APPROPRIATE END POINT(ASYNC)
    let url = "http://localhost/public/project/leader/project/feedback/messages";
    let requestBody = {
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
        projectLeaderFeedbackForumConnection.close();
    }
}

function appendMessage(type, parent_div, message) {

    let message_div = document.createElement('div'); // message

    if (type === 'OUT') {
        message_div.setAttribute('class', 'outgoing-message');
    } else if (type === 'IN') {
        message_div.setAttribute('class', 'incoming-message');
    } else {
        console.error('NOT A VALID MESSAGE TYPE');
        message_div = undefined;
        return;
    }

    let sender_details = document.createElement('div'); // sender details div
    sender_details.setAttribute('class', 'sender-details');

    let sender_profile_picture = document.createElement('img'); // sender profile picture img tag
    sender_profile_picture.src = message.sender_profile_picture;

    let sender_username = document.createElement('h5'); // sender user name heading
    sender_username.innerText = message.sender_username;

    let date_time = document.createElement('p'); // date time paragraph tag
    date_time.innerText = message.stamp;

    let message_content = document.createElement('p'); // message content
    message_content.setAttribute('class', 'message-content');
    message_content.innerText = message.msg;

    // adding elements
    sender_details.appendChild(sender_profile_picture);
    sender_details.appendChild(sender_username);
    sender_details.appendChild(date_time);

    message_div.appendChild(sender_details);
    message_div.appendChild(message_content);
    parent_div.insertAdjacentElement("afterbegin", message_div);
}