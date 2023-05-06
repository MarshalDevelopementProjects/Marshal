// Client profile picture img tag
const UserProfilePictureImg = document.getElementById("profile-picture-img");

// project details set up elements
const ProjectNameHeading = document.getElementById('project-name-heading');
const ProjectDescriptionParagraph = document.getElementById('project-description-paragraph');
const StartDateParagraph = document.getElementById('start-date');
const EndDateParagraph = document.getElementById('end-date');

// Feedback forum divs
const FeedbackMessageContainerDiv = document.getElementById('feedback-message-container');
const FeedbackMessageInputForm = document.getElementById('feedback-message-input-form');

let projectID = jsonData.project_id;
let userData = jsonData.user_data;
// let project_details = jsonData.project_details;
let date = new Date();

document.body.onload = async () => { await onLoad();};

// For today's date;
Date.prototype.today = function () {
    return ((this.getDate() < 10)?"0":"") + this.getDate() +"/"+(((this.getMonth()+1) < 10)?"0":"") + (this.getMonth()+1) +"/"+ this.getFullYear();
}

// For the time now
Date.prototype.timeNow = function () {
    return ((this.getHours() < 10)?"0":"") + this.getHours() +":"+ ((this.getMinutes() < 10)?"0":"") + this.getMinutes() +":"+ ((this.getSeconds() < 10)?"0":"") + this.getSeconds();
}

let clientFeedbackForumConnection = new WebSocket(`ws://localhost:8080/projects/feedback?project=${projectID}`);

clientFeedbackForumConnection.onopen = (event) => {
    console.log(event.data);
}

clientFeedbackForumConnection.onclose = (event) => {
    console.log(event.data);
}

clientFeedbackForumConnection.onerror = (event) => {
    console.error(event.data);
}

clientFeedbackForumConnection.onmessage = (event) => {
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



// Set up function
async function onLoad() {
    pageSetup(jsonData);
    await createFeedbackMessages();
}

function pageSetup(args) {
    if (args.user_data && args.user_data.profile_picture) {
        UserProfilePictureImg.setAttribute("src", args.user_data.profile_picture);
    }

    if(args.project_details && args.project_details[0]) {
        let project_details = args.project_details[0];
        if(project_details.project_name) ProjectNameHeading.innerText = project_details.project_name;
        if(project_details.description) ProjectDescriptionParagraph.innerText = project_details.description;

        // TODO: convert the dates to the progress bar case
        // TODO: get the progress bar data and set the progress bar as well

        if(project_details.start_on) StartDateParagraph.innerText = project_details.start_on;
        if(project_details.end_on) EndDateParagraph.innerText = project_details.end_on;
    }
}

// have to get the message data from the backend and then load them to the
// chat forum use the GET end points
async function createFeedbackMessages() {
    // TODO: GET ALL THE  MESSAGES FROM THE APPROPRIATE END POINT(ASYNC)
    let url = "http://localhost/public/project/client/project/feedback/messages";
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
            // TODO: ATTACH THE MESSAGES TO THE FORUM
            if (data.messages.length > 0) {
                data.messages.forEach(
                    message => {
                        // TODO: ATTACH THE MESSAGES TO THE FORUM
                        console.log(message);
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
        clientFeedbackForumConnection.close();
    }
}

function closeConnection() {
    // TODO: CLOSE THE POP-UP OR DO SOMETHING ELSE
    clientFeedbackForumConnection.close();
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
    if (formObj.feedback !=="") {
        await sendMessages(formObj.feedback);
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

    // TODO: SEND THE MESSAGE THROUGH THE SERVER
    clientFeedbackForumConnection.send(JSON.stringify(msgObj));

    // TODO: ATTACH THE FEEDBACK MESSAGE TO THE MESSAGING FORUM
    appendMessage('OUT', FeedbackMessageContainerDiv, msgObj);

    // TODO: SEND THE MESSAGE TO THE APPROPRIATE END POINT(ASYNC)
    let url = "http://localhost/public/project/client/project/feedback/messages";
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
        clientFeedbackForumConnection.close();
    }
}


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

// Function used to generate the PDF
function generateProjectReport() {
}
