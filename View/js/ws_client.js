// WS connections
// =============================================================================================================

// Project leader connections

// need to get the project id
let projectID = 12;
let taskID = 10;
let groupID = 14;

// project messaging forum connection
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
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

projectLeaderForumConnection.onmessage = async (event) => {
    // A JSON STRING WILL BE SENT FROM THE SENDER PARSE IT AND TAKE THE DATA
    console.log(event.data);

    // Message data must be a json string of this form
    // {
    //      username: "USERNAME OF THE SENDER",
    //      profile_picture: "PATH TO THE PROFILE PICTURE OF THE SENDER",
    //      date_time: "DATE TIME STRING",
    //      message: "BODY MESSAGE "
    // }
    // TODO: ATTACH THE INCOMING DATA TO THE FORUM
    async function onMessage(messageData) {
        let message_data = await messageData.json();
        console.log(message_data.username);
        console.log(message_data.profile_picture);
        console.log(message_data.date_time);
        console.log(message_data.message);
    }
    await onMessage(event.data);
}

async function projectForumOnStartUp() {
    // TODO: SEND A GET REQUEST TO THE
    let url = "http://localhost/public/project/leader/";
}



// have to get the message data from the backend and then load them to the
// chat forum use the GET end points
async function onStartUp(messageData, type) {
    // TODO: GET ALL THE  MESSAGES FROM THE APPROPRIATE END POINT(ASYNC)
    let url = "http://localhost/public/project/leader/";
    switch (type) {
        case "PROJECT_MESSAGE": {
            url += "forum/messages";
        } break;
        case "PROJECT_FEEDBACK_MESSAGE": {
            url += "project/feedback/messages";
        } break;
        case "GROUP_FEEDBACK_MESSAGE": {
            url += "group/feedback/messages";
        } break;
        case "PROJECT_TASK_FEEDBACK_MESSAGE": {
            url += `task/feedback/messages?task_id=${taskID}`;
        } break;
        default: {
            console.error("NOT A VALID MESSAGE TYPE");
            return;
        }
    }

    try{
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
async function sendMessages(messageData, type) {
    // TODO: ATTACH THE MESSAGE TO THE MESSAGING FORUM

    // TODO: SEND THE MESSAGE
    projectLeaderForumConnection.send(JSON.stringify(messageData));

    // TODO: SEND THE MESSAGE TO THE APPROPRIATE END POINT(ASYNC)
    let url = "http://localhost/public/project/leader/";
    let requestBody = {};

    switch (type) {
        case "PROJECT_MESSAGE": {
            url = "forum/messages";
            requestBody = {
               message: messageData.message
            };
        } break;
        case "PROJECT_FEEDBACK_MESSAGE": {
            url = "feedback/messages";
            requestBody = {
                message: messageData.message
            };
        } break;
        case "GROUP_FEEDBACK_MESSAGE": {
            url = "group/feedback/messages";
            requestBody = {
                message: messageData.message
            };
        } break;
        case "PROJECT_TASK_FEEDBACK_MESSAGE": {
            url = "task/feedback/messages";
            requestBody = {
                task_id: messageData.task_id,
                message: messageData.message
            };
        } break;
        default: {
            console.error("NOT A VALID MESSAGE TYPE");
            return;
        }
    }

   try{
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

/*
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// project feedback messaging forum connection

let projectLeaderFeedbackConnection = new WebSocket(`ws://localhost:8080/projects/feedback?project=${projectID}`);
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

// project task feedback messaging forum connection
let projectLeaderTaskFeedbackConnection = new WebSocket(`ws://localhost:8080/tasks/feedback?project=${projectID}&task=${taskID}`);

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// project group feedback messaging forum connection
let projectLeaderGroupFeedbackConnection = new WebSocket(`ws://localhost:8080/projects/feedback?project=${projectID}&group=${groupID}`);

// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++





// =============================================================================================================
// Project member connections

// project messaging forum connection
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
let projectMemberForumConnection = new WebSocket(`ws://localhost:8080/projects/forum?project=${projectID}`);

// project task feedback messaging forum connection
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
let projectMemberTaskFeedbackConnection = new WebSocket(`ws://localhost:8080/projects/forum?project=${projectID}`);

// project messaging forum
// +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
