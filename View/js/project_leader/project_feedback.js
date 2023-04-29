// Message container and other functions
const FeedbackMessageContainerDiv = document.getElementById('feedback-message-container');
const FeedbackMessageInputForm = document.getElementById('client-feedback-form');

let projectID = jsonData.project_id;
console.log(projectID);
let userData = jsonData.user_data;
let date = new Date();

document.body.onload = async () => { await onSlideLoad();};

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
            appendMessage('IN', FeedbackMessageContainerDiv, message_data);
    }
    onMessage(event.data);
}

// have to get the message data from the backend and then load them to the
// chat forum use the GET end points
async function onSlideLoad() {
    await createFeedbackMessageForum();
}

async function createFeedbackMessageForum() {
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
            let data = await response.json();
            // TODO: ATTACH THE MESSAGES TO THE FORUM
            if (data.messages.length > 0) {
                data.messages.forEach(
                    message => {
                        // TODO: ATTACH THE MESSAGES TO THE FORUM
                        // check whether the messages are incoming messages or outgoing messages
                        if (message.sender_username !== userData.username) {
                            appendMessage('IN', FeedbackMessageContainerDiv, message);
                        } else {
                            appendMessage('OUT', FeedbackMessageContainerDiv, message);
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

FeedbackMessageInputForm.addEventListener('submit', async (event) => {
    event.preventDefault();
    let formObj = Object.fromEntries(new FormData(FeedbackMessageInputForm));
    console.log(formObj);
    if (formObj.feedbackMessage !=="") {
        await sendMessages(formObj.feedbackMessage);
    }
    FeedbackMessageInputForm.reset();
});


async function sendMessages(msg) {
    // TODO: ATTACH THE MESSAGE TO THE MESSAGING FORUM

    // creating the message object
    let msgObj = {
        sender_username: userData.username,
        sender_profile_picture: userData.profile_picture,
    };

    msgObj.msg = msg;
    msgObj.stamp = `${date.today()} ${date.timeNow()}`;

    // TODO: SEND THE MESSAGE
    projectLeaderFeedbackForumConnection.send(JSON.stringify(msgObj));
    appendMessage('OUT', FeedbackMessageContainerDiv, msgObj);
    console.log(msgObj);

    // TODO: SEND THE MESSAGE TO THE APPROPRIATE END POINT(ASYNC)
    let url = "http://localhost/public/project/leader/project/feedback/messages";
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
        projectLeaderFeedbackForumConnection.close();
    }
}

/*
 <!-- THIS IS THE FORMAT OF AN INCOMING MESSAGE -->
 <div class="incomming-feedback">
     <img src="/View/images/dp.png" alt="">
     <div class="incomming-feedback-message">
         <p class="incomming-feedbacks">Hello How are you</p>
         <p class="incomming-time">4.14 PM</p>
     </div>
 </div>

 <!-- THIS IS THE FORMAT OF AN OUTGOING MESSAGE -->
 <div class="outgoing-feedback">
     <p class="outgoing-feedbacks">Hello How are you</p>
     <p class="outgoing-time">4.14 PM</p>
 </div>

    <div class="incomming-feedback-message">
         <p class="incomming-feedbacks">Hello How are you</p>
         <p class="incomming-time">4.14 PM</p>
     </div>

*/

function appendMessage(type, parent_div, message) {


    console.log(message);
    let message_div = document.createElement('div'); // message

    // main div
    if (type === 'OUT') {
        message_div.setAttribute('class', 'outgoing-feedback');

        let sender_username = document.createElement('h5'); // sender user name heading
        sender_username.innerText = message.sender_username;

        let sender_profile_picture = document.createElement('img'); // sender profile picture img tag
        sender_profile_picture.src = message.sender_profile_picture;

        let date_time = document.createElement('p'); // date time paragraph tag
        date_time.setAttribute('class', 'outgoing-time'); // date time paragraph tag
        date_time.innerText = message.stamp;

        let message_content = document.createElement('p'); // message content
        message_content.setAttribute('class', 'outgoing-feedbacks');
        message_content.innerText = message.msg;

        // adding elements
        // message_div.appendChild(sender_profile_picture);
        // message_div.appendChild(sender_username);
        message_div.appendChild(message_content);
        message_div.appendChild(date_time);

    } else if (type === 'IN') {
        message_div.setAttribute('class', 'incomming-feedback');

        let sender_username = document.createElement('h5'); // sender user name heading
        sender_username.innerText = message.sender_username;

        let sender_profile_picture = document.createElement('img'); // sender profile picture img tag
        sender_profile_picture.src = message.sender_profile_picture;

        let inner_message_div = document.createElement('div'); // message
        inner_message_div.setAttribute('class', 'incomming-feedback-message');

        let date_time = document.createElement('p'); // date time paragraph tag
        date_time.setAttribute('class', 'incomming-time'); // date time paragraph tag
        date_time.innerText = message.stamp;

        let message_content = document.createElement('p'); // message content
        message_content.setAttribute('class', 'incomming-feedbacks');
        message_content.innerText = message.msg;

        // adding elements
        inner_message_div.appendChild(message_content);
        inner_message_div.appendChild(date_time);
        message_div.appendChild(sender_profile_picture);
        message_div.appendChild(sender_username);
        message_div.appendChild(inner_message_div);
    } else {
        console.error('NOT A VALID MESSAGE TYPE');
        message_div = undefined;
        return;
    }
    parent_div.insertAdjacentElement("afterbegin", message_div);
}