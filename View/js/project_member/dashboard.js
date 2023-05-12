console.log(jsonData)
/** 
 * @jsonData {array} : It returns all details that relevant to the page
 * From below lines of code,
 *      get the details (all tasks related to the project) and divide them into categories based on the status of the task
 *      then sort based on the priority of the task
*/

todoBoard = document.querySelector('.todo .tasks');
ongoingBoard = document.querySelector('.ongoing .tasks');
reviewBoard = document.querySelector('.review .tasks');
doneBoard = document.querySelector('.done .tasks');

var todoTasks = jsonData['tasks']['todoTasks'];
var ongoingTasks = jsonData['tasks']['ongoingTasks'];
var reviewTasks = jsonData['tasks']['reviewTasks'];
var doneTasks = jsonData['tasks']['doneTasks'];

/**
 * Sort the tasks by priority
 */
let priorities = { "high": 3, "medium": 2, "low": 1 };

if(todoTasks){
    todoTasks = Object.values(todoTasks).sort((a, b) => {
        return priorities[b.priority] - priorities[a.priority];
    });
}
if(ongoingTasks){
    ongoingTasks = Object.values(ongoingTasks).sort((a, b) => {
        return priorities[b.priority] - priorities[a.priority];
    });
}
if(reviewTasks){
    reviewTasks = Object.values(reviewTasks).sort((a, b) => {
        return priorities[b.priority] - priorities[a.priority];
    });
}
if(doneTasks){
    doneTasks = Object.values(doneTasks).sort((a, b) => {
        return priorities[b.priority] - priorities[a.priority];
    });
}

/**
 * Set the html code based on the data that passed
 * There are some additional details on some status, as example ongoing task has a profile picture which is working on it
 * So we have to do this individually
 */
var todoTasksCode = "";

if(todoTasks){
    todoTasks.forEach(task => {
        todoTasksCode += `<div class="task" draggable="true">
                                <div class="top-task">
                                    <h4>${task['task_name']}</h4>
                                    <p class="priority-${task['priority']}">${task['priority']}</p>
                                </div>
                                <p class="task-description">${task['description']}</p>
                                <div class="bottom-task style="display:flex">
                                    <p class="deadline">${task['deadline'].split(' ')[0]}</p>
                                </div>
                            </div>`
    })
}
todoBoard.innerHTML = todoTasksCode;

var ongoingTasksCode = "";

if(ongoingTasks){
    ongoingTasks.forEach(task => {
        let access = ""
        if(task['member_id'] != task['userId']){
            access = ' style="pointer-events: none"'
        }
        ongoingTasksCode += `<div class="task" draggable="true"${access}>
                                <div class="top-task">
                                    <h4>${task['task_name']}</h4>
                                    <p class="priority-${task['priority']}">${task['priority']}</p>
                                </div>
                                <p class="task-description">${task['description']}</p>
                                <div class="bottom-task style="display:flex">
                                    <p class="deadline">${task['deadline'].split(' ')[0]}</p>
                                    <img id="member-profile" src="${task['profile']}" alt="">
                                </div>
                            </div>`
    })
}
ongoingBoard.innerHTML = ongoingTasksCode;

var reviewTasksCode = "";

if(reviewTasks){
    reviewTasks.forEach(task => {
        let access = ""
        if(task['member_id'] != task['userId']){
            access = ' style="pointer-events: none"'
        }
        reviewTasksCode += `<div class="task" draggable="true"${access}>
                                <div class="top-task">
                                    <h4>${task['task_name']}</h4>
                                    <p class="priority-${task['priority']}">${task['priority']}</p>
                                </div>
                                <p class="task-description">${task['description']}</p>
                                <div class="bottom-task style="display:flex">
                                    <p id="Pending">Pending...</p>
                                    <img id="member-profile" src="${task['profile']}" alt="">
                                </div>
                            </div>`
    })
}
reviewBoard.innerHTML = reviewTasksCode;

var doneTasksCode = "";

if(doneTasks){
    doneTasks.forEach(task => {
        let access = ""
        if(task['memberId'] != task['userId']){
            access = ' style="pointer-events: none"'
        }
        doneTasksCode += `<div class="task" draggable="true" style="pointer-events: none">
                            <div class="top-task">
                                <h4>${task['task_name']}</h4>
                                <p class="priority-${task['priority']}">${task['priority']}</p>
                            </div>
                            <p class="task-description">${task['description']}</p>
                            <div class="bottom-task style="display:flex">
                                <p id="Pending">Done</p>
                                <img id="member-profile" src="${task['profile']}" alt="">
                            </div>
                        </div>`
    })
}
doneBoard.innerHTML = doneTasksCode;

/**
 * 
 * @param {string} boardName The name of the board (Todo, Ongoing, Review, Done)
 * @param {string} taskName The name of the task
 * @returns {Array} The details of the task that passed through jsonData
 */
const getTaskDetails = (boardName, taskName) => {
    return boardName.find(element => element.task_name === taskName)
}

/**
 * Send the confirmation message to project leader
 * @param {string} taskName name of the task to be confirmed
 * @param {string} message message to be sent when the task is completed
 * @param {*} date date the task was cmpleted
 * @param {*} time time the task was completed
 */

const sendConfirmationFunction = (taskName, message, date, time) => {
    fetch("http://localhost/public/projectmember/sendconfirmation", {
        withCredentials: true,
        credentials: "include",
        mode: "cors",
        method: "POST",
        body: JSON.stringify({
            "task_name" : taskName.toString(),
            "confirmation_message" : message,
            "confirmation_type" : message ? "message" : "file",
            "date" : date,
            "time" : time
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log(data);
        location.reload();
    })
    .catch((error) => {
        console.error(error)
    });
}

/**
 * Build drag and drop tasks feature
 */

const tasks = document.querySelectorAll('.task');
const boards = document.querySelectorAll('.tasks');

var startX, endX;
/**
 * @hasAccess {boolean}
 * member does not allow to drag all tasks anywhere on anytime, they have restrictions
 * Member has access only for their own task
 */
var hasAccess = true;
var oldBoard, newBoard;

tasks.forEach(task => {
    /**
     * when task start dragging, set the old board and the position
     */
    task.addEventListener('dragstart', (event)=>{
        task.classList.add('dragging');
        startX = event.clientX;

        oldBoard = task.parentNode.parentNode.className.split(' ')[0].toUpperCase();

    })
    /**
     * task finish dragging, drop it down
     */
    task.addEventListener('dragend', (event)=>{
        task.classList.remove('dragging');
        endX = event.clientX;

        newBoard = task.parentNode.parentNode.className.split(' ')[0].toUpperCase()
        if(newBoard === "TODO") newBoard = "TO-DO"
        if(oldBoard === "TODO") oldBoard = "TO-DO"

        if(newBoard === "ONGOING" && oldBoard === "TO-DO"){
            
            let draggedTaskName = task.firstElementChild.firstElementChild.textContent;
            
            fetch("http://localhost/public/projectmember/pickuptask", {
                withCredentials: true,
                credentials: "include",
                mode: "cors",
                method: "POST",
                body: JSON.stringify({
                    "task_name" : draggedTaskName.toString()
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                location.reload();
            })
            .catch((error) => {
                console.error(error)
            });
        }else if(newBoard === "REVIEW" && oldBoard === "ONGOING"){
            // const confirmationPopup = document.querySelector('.confirmation-popup')
            // const confirmationPopupCloseBtn = document.querySelector('.confirmation-popup .close-area i')

            // const sendConfirmation = document.querySelector('.confirmation-popup .input-area button')
            // const confirmationMessage = document.getElementById('confirmationMessage')

            // let draggedTaskName = task.firstElementChild.firstElementChild.textContent;
            // var message = ""

            // /**
            //  * get current date and time
            //  */            
            // var date = new Date();
            // var year = date.getFullYear();
            // var month = (date.getMonth() + 1).toString().padStart(2, '0');
            // var day = date.getDate().toString().padStart(2, '0');
            // var formattedDate = year + '-' + month + '-' + day;

            // var time = date.toLocaleTimeString();

            // confirmationMessage.addEventListener('input', () => {
            //     message = confirmationMessage.value
            // })


            // sendConfirmation.addEventListener('click', () => {
            //     sendConfirmationFunction(draggedTaskName, message, formattedDate, time)
            // })
            // confirmationMessage.addEventListener('keyup', (event) =>{
            //     if(event.keyCode === 13){
            //         sendConfirmationFunction(draggedTaskName, message, formattedDate, time)
            //     }
            // })


            // confirmationPopup.classList.add('active')
            // confirmationPopupCloseBtn.addEventListener('click', () => {
            //     confirmationPopup.classList.remove('active')
            //     location.reload();
            // })
            showConfirmationPopup(draggedTaskName)
            
        }
                
    })
})



boards.forEach(board => {

    board.addEventListener('dragover', event => {
        event.preventDefault();

        const task = document.querySelector('.dragging');

        var dragDistance = event.clientX - startX;
        /**
         * Restrict the dragging to the member
         * Member cannot be dragged to left side and jump the boards
         */
        if(dragDistance > 0 && dragDistance < 350 && event.clientX < 1100){

            var firstChild = board.firstChild
            if(firstChild){      
                board.insertBefore(task, firstChild)
            }else{
                board.appendChild(task);
            }
            
        }
    })
})



function pickupTask(task){
    fetch("http://localhost/public/projectmember/pickuptask", {
        withCredentials: true,
        credentials: "include",
        mode: "cors",
        method: "POST",
        body: JSON.stringify({
            "task_name" : task
        })
    })
    .then(response => response.json())
    .then(data => {
        const todoTaskPopup = document.querySelector('.TO-DO-task-details')
        
        todoTaskPopup.classList.remove('active')
        location.reload();
    })
    .catch((error) => {
        console.error(error)
    });
}

async function getFeedbacks(taskDetails, board){

    // console.log(taskDetails)
    let feedbackMessages = ""

    return fetch(`http://localhost/public/projectmember/taskfeedback?task=${taskDetails['task_id']}`, {
        withCredentials: true,
        credentials: "include",
        mode: "cors",
        method: "GET"
    })
    .then(response => response.json())
    .then(data => {
        // console.log(data['message'])
        data['message'].reverse().forEach(feedback => {
            if(feedback['type'] == 'incoming'){
                feedbackMessages += `<div class="${board}-incomming-feedback">
                                        <div class="${board}-incomming-feedback-sender">
                                            <img src="${feedback['profile']}" alt="">
                                        </div>
                                        <div class="${board}-incomming-feedback-message">
                                            <p class="${board}-incomming-feedbacks">${feedback['msg']}</p>
                                            <p class="${board}-incomming-time">${feedback['stamp'].split(" ")[1]}</p>
                                        </div>
                                    </div>`
            }else if(feedback['type'] == 'outgoing'){
                feedbackMessages += `<div class="${board}-outgoing-feedback">
                                        <div class="${board}-outgoing-feedback-message">
                                            <p class="${board}-outgoing-feedbacks">${feedback['msg']}</p>
                                            <p class="${board}-outgoing-time">${feedback['stamp'].split(" ")[1]}</p>
                                        </div>
                                    </div>`
            }
        });

        return feedbackMessages

    })
    .catch((error) => {
        console.error(error)
    })

}

let popped = false;

function showConfirmationPopup(taskName){

    const confirmationPopup = document.querySelector('.confirmation-popup'),
        confirmationPopupCloseBtn = document.querySelector('.confirmation-popup .close-area i'),
        confirmationMessage = document.getElementById('confirmationMessage'),
        sendConfirmation = document.querySelector('.confirmation-popup .input-area button')

    confirmationPopup.classList.add('active')

    var message = ""

    // get current date and time
    var date = new Date();
    var year = date.getFullYear();
    var month = (date.getMonth() + 1).toString().padStart(2, '0');
    var day = date.getDate().toString().padStart(2, '0');
    var formattedDate = year + '-' + month + '-' + day;

    var time = date.toLocaleTimeString();

    confirmationMessage.addEventListener('input', () => {
        message = confirmationMessage.value
    })

    if(message != null){
        sendConfirmation.addEventListener('click', () => {
            sendConfirmationFunction(taskName, message, formattedDate, time)
            return
        })
        confirmationMessage.addEventListener('keyup', (event) =>{
            if(event.keyCode === 13){
                sendConfirmationFunction(taskName, message, formattedDate, time)
                return
            }
        })
    }
    
    confirmationPopupCloseBtn.addEventListener('click', () => {
        confirmationPopup.classList.remove('active')
        location.reload();
    })
}


function setTimeInterval(taskDetails, messagesClass, feedbackFormInput, board) {
    const messagesArea = document.querySelector(messagesClass)

    var timeInterval = setInterval(() => {
        console.log(taskDetails)

        feedbackFormInput.focus()

        // let feedbacksCode = getFeedbacks(taskDetails, board)
        // feedbacksCode.then(code => messagesArea.innerHTML = code)


        fetch(`http://localhost/public/projectmember/taskfeedback?task=${taskDetails['task_id']}`, {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "GET"
        })
        .then(response => response.json())
        .then(data => {
            feedbackMessages = ""
            // console.log(data['message'])
            data['message'].reverse().forEach(feedback => {
                if(feedback['type'] == 'incoming'){
                    feedbackMessages += `<div class="${board}-incomming-feedback">
                                            <div class="${board}-incomming-feedback-sender">
                                                <img src="${feedback['profile']}" alt="">
                                            </div>
                                            <div class="${board}-incomming-feedback-message">
                                                <p class="${board}-incomming-feedbacks">${feedback['msg']}</p>
                                                <p class="${board}-incomming-time">${feedback['stamp'].split(" ")[1]}</p>
                                            </div>
                                        </div>`
                }else if(feedback['type'] == 'outgoing'){
                    feedbackMessages += `<div class="${board}-outgoing-feedback">
                                            <div class="${board}-outgoing-feedback-message">
                                                <p class="${board}-outgoing-feedbacks">${feedback['msg']}</p>
                                                <p class="${board}-outgoing-time">${feedback['stamp'].split(" ")[1]}</p>
                                            </div>
                                        </div>`
                }
            });
    
            messagesArea.innerHTML = feedbackMessages
    
        })
        .catch((error) => {
            console.error(error)
        })

        console.log(popped)
        if(popped == false){
            messagesArea.innerHTML = ""
            clearInterval(timeInterval)
        }
    }, 500)

}

function showTodoPopup(taskDetails){

    const todoTaskPopup = document.querySelector('.TO-DO-task-details'),
        taskName = document.querySelector('.todo-top-bar h3'),
        taskPriority = document.querySelector('.todo-top-bar p'),
        taskDescription = document.querySelector('.todo-task-details-description'),
        taskDeadline = document.querySelector('.todo-task-details-deadline'),
        pickupbtn = document.getElementById('pickup-task-btn'),
        cancelBtn = document.getElementById('cancel-todo-task-details')

    todoTaskPopup.classList.add('active')
    taskName.innerText = taskDetails['task_name']
    taskPriority.innerText = taskDetails['priority']
    taskPriority.classList.add(taskDetails['priority'])
    taskDescription.innerText = taskDetails['description']
    taskDeadline.innerText = "Deadline : " + taskDetails['deadline'].split(' ')[0]

    pickupbtn.addEventListener("click", () => {
        pickupTask(taskName.innerText)
    })
    cancelBtn.addEventListener('click', () => {
        todoTaskPopup.classList.remove('active')
    })
}

// show up the ongoing popup
let feedbackFormSubmitHandler = null

function showOngoingPopup(taskDetails){
    const ongoingTaskPopup = document.querySelector('.Ongoing-task-details'),
        taskName = document.querySelector('.ongoing-top-bar h3'),
        taskPriority = document.querySelector('.ongoing-top-bar p'),
        taskDescription = document.querySelector('.ongoing-task-details-description'),
        taskDeadline = document.querySelector('.ongoing-task-details-deadline'),
        taskFeedbackMessages = document.querySelector('.ongoing-task-feedback-messages'),
        taskFeedbackForm = document.querySelector('#ongoing-task-feedback-form'),
        taskFeedbackFormInput = document.querySelector('#ongoing-task-feedback-form-input'),

        cancelBtn = document.getElementById('cancel-ongoing-task-details'),
        finishBtn = document.getElementById('finishTaskBtn')

    let feedbacksCode = getFeedbacks(taskDetails, "ongoing")
    taskFeedbackFormInput.focus()
 
    // get feedback messages
    feedbacksCode
    .then(code => taskFeedbackMessages.innerHTML = code)
    .catch((error)=>console.error(error))

    ongoingTaskPopup.classList.add('active');
    
    taskName.innerText = taskDetails['task_name']
    taskPriority.innerText = taskDetails['priority']
    taskPriority.classList.add(taskDetails['priority'])
    taskDescription.innerText = taskDetails['description']
    taskDeadline.innerText = "Deadline : " + taskDetails['deadline'].split(' ')[0]

    if(feedbackFormSubmitHandler){
        taskFeedbackForm.removeEventListener('submit', feedbackFormSubmitHandler)
    }

    feedbackFormSubmitHandler = (event) => {
        event.preventDefault()

        const feedbackForm = new FormData(event.target)
        const feedbackMessage = feedbackForm.get('feedbackMessage')

        if(feedbackMessage){
            fetch("http://localhost/public/projectmember/taskfeedback", {
                withCredentials: true,
                credentials: "include",
                mode: "cors",
                method: "POST",
                body: JSON.stringify({
                    "feedbackMessage" : feedbackMessage,
                    "task_id" : taskDetails['task_id']
                })
            })
            .then(response => response.json())
            .then(data => {
                
                taskFeedbackFormInput.focus()
                taskFeedbackForm.reset()

                // console.log(data)
            })
            .catch((error) => {
                console.error(error)
            })
        }else{
            return
        }
        event.target.reset()
        return
    }
    
    taskFeedbackForm.addEventListener('submit', feedbackFormSubmitHandler)


    finishBtn.addEventListener('click', () => {
        popped = false
        showConfirmationPopup(taskDetails['task_name'])
    })
    cancelBtn.addEventListener('click', () => {
        popped = false;
        taskFeedbackMessages.innerHTML = ""
        ongoingTaskPopup.classList.remove('active')
        return
    })

    setTimeInterval(taskDetails, ".ongoing-task-feedback-messages", taskFeedbackFormInput, "ongoing")
}

function showReviewPopup(taskDetails){
    const reviewTaskPopup = document.querySelector('.Review-task-details'),
        taskName = document.querySelector('.review-top-bar h3'),
        taskPriority = document.querySelector('.review-top-bar p'),
        taskDescription = document.querySelector('.review-task-details-description'),
        taskDeadline = document.querySelector('.review-task-details-deadline'),
        taskFeedbackMessages = document.querySelector('.review-task-feedback-messages'),
        taskFeedbackForm = document.querySelector('#review-task-feedback-form'),
        taskFeedbackFormInput = document.querySelector('#review-task-feedback-form-input'),
        continueBtn = document.querySelector('#continueBtn'),
        completedMessage = document.querySelector('#completedMessage')

    let feedbacksCode = getFeedbacks(taskDetails, "review")
    taskFeedbackFormInput.focus()

    feedbacksCode
    .then(code => taskFeedbackMessages.innerHTML = code)
    .catch((error) => {console.error(error)})

    reviewTaskPopup.classList.add('active');
    taskName.innerText = taskDetails['task_name']
    taskPriority.innerText = taskDetails['priority']
    taskPriority.classList.add(taskDetails['priority'])
    taskDescription.innerText = taskDetails['description']
    taskDeadline.innerText = "Deadline : " + taskDetails['deadline'].split(' ')[0]
    completedMessage.innerText = "Confirmation message : " + taskDetails['confirmationMessage']


    if(feedbackFormSubmitHandler){
        taskFeedbackForm.removeEventListener('submit', feedbackFormSubmitHandler)
    }

    feedbackFormSubmitHandler = (event) => {
        event.preventDefault()

        const feedbackForm = new FormData(event.target)
        const feedbackMessage = feedbackForm.get('feedbackMessage')

        if(feedbackMessage){
            fetch("http://localhost/public/projectmember/taskfeedback", {
                withCredentials: true,
                credentials: "include",
                mode: "cors",
                method: "POST",
                body: JSON.stringify({
                    "feedbackMessage" : feedbackMessage,
                    "task_id" : taskDetails['task_id']
                })
            })
            .then(response => response.json())
            .then(data => {
                
                taskFeedbackFormInput.focus()
                taskFeedbackForm.reset()
            })
            .catch((error) => {
                console.error(error)
            })
        }else{
            return
        }
        event.target.reset()
        return
    }
    
    taskFeedbackForm.addEventListener('submit', feedbackFormSubmitHandler)

    continueBtn.addEventListener('click', () => {
        popped = false
        reviewTaskPopup.classList.remove('active')
    })
            
    setTimeInterval(taskDetails, ".review-task-feedback-messages", taskFeedbackFormInput, "review")
    
}


/**
 * When tasks click , it must show details of it
 */
tasks.forEach(task => {
    task.addEventListener('click',async event => {
        var position = event.clientX;

        // get task name
        task.classList.add('clicked');
        var taskName = document.querySelector('.clicked .top-task h4').innerText;
        var taskDetails;

        // check the board by position
        if(position > 292 && position < 514){

            taskDetails = getTaskDetails(todoTasks, taskName)
            showTodoPopup(taskDetails)

        }else if(position > 562 && position < 784){
            popped = true;
            taskDetails = getTaskDetails(ongoingTasks, taskName)
            showOngoingPopup(taskDetails)

        }else if(position > 832 && position < 1054){
            popped = true;
            taskDetails = getTaskDetails(reviewTasks, taskName)
            showReviewPopup(taskDetails)

        }

        console.log(taskDetails);

        task.classList.remove('clicked');
    })
})

console.log(jsonData)

// set project name
const projectName = document.querySelector('.project-name')
projectName.innerText = jsonData['projectName']