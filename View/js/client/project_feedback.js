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
    }
    onMessage(event.data);
}

let msgObj = {
    username:   userData.username,
    profile_picture: userData.profile_picture,
};

// have to get the message data from the backend and then load them to the
// chat forum use the GET end points
async function onStartUp(type) {
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
async function sendMessages(msg) {
    // TODO: ATTACH THE MESSAGE TO THE MESSAGING FORUM

    msgObj.message = msg;
    msgObj.date_time = `${date.today()} ${date.timeNow()}`;
    // TODO: SEND THE MESSAGE
    clientFeedbackForumConnection.send(JSON.stringify(msgObj));
    console.log(msgObj);

    // TODO: SEND THE MESSAGE TO THE APPROPRIATE END POINT(ASYNC)
    let url = "http://localhost/public/project/client/project/feedback/messages";
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
        clientFeedbackForumConnection.close();
    }
}