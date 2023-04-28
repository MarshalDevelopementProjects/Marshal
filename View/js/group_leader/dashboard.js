console.log(jsonData);

// set tasks of the project


todoBoard = document.querySelector('.todo .tasks'),
ongoingBoard = document.querySelector('.ongoing .tasks'),
reviewBoard = document.querySelector('.review .tasks'),
doneBoard = document.querySelector('.done .tasks');

var todoTasks = jsonData['groupTasks']['todoTasks'];
var ongoingTasks = jsonData['groupTasks']['ongoingTasks'];
var reviewTasks = jsonData['groupTasks']['reviewTasks'];
var doneTasks = jsonData['groupTasks']['doneTasks'];


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
        ongoingTasksCode += `<div class="task" draggable="true">
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
        reviewTasksCode += `<div class="task" draggable="true">
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


const getTaskDetails = (boardName, taskName) => {
    return boardName.find(element => element.task_name === taskName)
}


let progressBar = document.querySelector('.cuircular-progress');
let progressValue = document.querySelector('.progress-value');

let value = 0;
let endValue = 65;
let speed = 100;

let progress = setInterval(() => {
    value++;
    progressValue.textContent = `${value}%`;
    progressBar.style.background = `conic-gradient(
        #924444 ${value * 3.6}deg,
        #be9191 ${value * 3.6}deg
    )`;
    if(value === endValue){
        clearInterval(progress);
    }
}, speed);




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

const rearangetask = function (taskName, newBoard){
    fetch("http://localhost/public/projectleader/rearangetask", {
                    withCredentials: true,
                    credentials: "include",
                    mode: "cors",
                    method: "POST",
                    body: JSON.stringify({
                        "task_name" : taskName,
                        "new_board" : newBoard
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



const tasks = document.querySelectorAll('.task');
const boards = document.querySelectorAll('.tasks');

var startX, endX, oldBoard, newBoard;

tasks.forEach(task => {
    task.addEventListener('dragstart', (event)=>{
        startX = event.clientX;
        oldBoard = task.parentNode.parentNode.className.split(' ')[0].toUpperCase()

        task.classList.add('dragging');
    })

    task.addEventListener('dragend', (event)=>{
        task.classList.remove('dragging');
        endX = event.clientX;

        newBoard = task.parentNode.parentNode.className.split(' ')[0].toUpperCase()

        if(newBoard === "TODO") newBoard = "TO-DO"
        if(oldBoard === "TODO") oldBoard = "TO-DO"
        let draggedTaskName = task.firstElementChild.firstElementChild.textContent;

        if(newBoard != oldBoard){
            if(oldBoard == "TO-DO" && newBoard == "ONGOING"){
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
                const confirmationPopup = document.querySelector('.confirmation-popup')
                const confirmationPopupCloseBtn = document.querySelector('.confirmation-popup .close-area i')
    
                const sendConfirmation = document.querySelector('.confirmation-popup .input-area button')
                const confirmationMessage = document.getElementById('confirmationMessage')
    
                let draggedTaskName = task.firstElementChild.firstElementChild.textContent;
                var message = ""
    
                /**
                 * get current date and time
                 */            
                var date = new Date();
                var year = date.getFullYear();
                var month = (date.getMonth() + 1).toString().padStart(2, '0');
                var day = date.getDate().toString().padStart(2, '0');
                var formattedDate = year + '-' + month + '-' + day;
    
                var time = date.toLocaleTimeString();
    
                confirmationMessage.addEventListener('input', () => {
                    message = confirmationMessage.value
                })
    
    
                sendConfirmation.addEventListener('click', () => {
                    sendConfirmationFunction(draggedTaskName, message, formattedDate, time)
                })
                confirmationMessage.addEventListener('keyup', (event) =>{
                    if(event.keyCode === 13){
                        sendConfirmationFunction(draggedTaskName, message, formattedDate, time)
                    }
                })
    
    
                confirmationPopup.classList.add('active')
                confirmationPopupCloseBtn.addEventListener('click', () => {
                    confirmationPopup.classList.remove('active')
                    location.reload();
                })
                
            }else if(newBoard == "DONE" && oldBoard == "REVIEW"){

                rearangetask(draggedTaskName.toString(), newBoard) 
            }
            if(event.clientX - startX < 0){
                rearangetask(draggedTaskName.toString(), newBoard) 
            }
            
        }
    })
})

boards.forEach(board => {
    board.addEventListener('dragover', e => {
        e.preventDefault();
        const afterElement = getDragAfterElement(board, e.clientY);
        const task = document.querySelector('.dragging');
        
        let dragDistance = e.clientX - startX;
        // leader can drag to left any far but not to forward
        if(dragDistance < 350){
            if(afterElement == null){
                board.appendChild(task);
            }else{
                board.insertBefore(task, afterElement);
            }
        }
    })
})

function getDragAfterElement(board, y){
    const draggableElements = [...board.querySelectorAll('.task:not(.dragging)')]

    return draggableElements.reduce((closest, child)=>{
        const box = child.getBoundingClientRect()
        const offset = y - box.top - box.height/2
        console.log(offset)
        if(offset < 0 && offset > closest.offset){
            return {offset: offset, element: child}
        }else{
            return closest
        }
    }, {offset: Number.NEGATIVE_INFINITY}).element
}




async function getFeedbacks(taskDetails, board){

    // console.log(taskDetails)
    let feedbackMessages = ""

    return fetch(`http://localhost/public/groupmember/taskfeedback?task=${taskDetails['task_id']}`, {
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


function setTimeInterval(taskDetails, messagesClass, feedbackFormInput, board) {
    const messagesArea = document.querySelector(messagesClass)

    var timeInterval = setInterval(() => {
        // console.log(taskDetails)

        feedbackFormInput.focus()

        // let feedbacksCode = getFeedbacks(taskDetails, board)
        // feedbacksCode.then(code => messagesArea.innerHTML = code)


        fetch(`http://localhost/public/groupmember/taskfeedback?task=${taskDetails['task_id']}`, {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "GET"
        })
        .then(response => response.json())
        .then(data => {
            feedbackMessages = ""
            console.log(data['message'])
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


let popped = false;

function assignMemberTodoTask(taskName, memberName){
    fetch("http://localhost/public/projectleader/assigntask", {
        withCredentials: true,
        credentials: "include",
        mode: "cors",
        method: "POST",
        body: JSON.stringify({
            "task_name" : taskName,
            "member_username" : memberName
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
function showConfirmationPopup(){

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

function showTodoPopup(taskDetails){

    const todoTaskPopup = document.querySelector('.TO-DO-task-details'),
        taskName = document.querySelector('.todo-top-bar h3'),
        taskPriority = document.querySelector('.todo-top-bar p'),
        taskDescription = document.querySelector('.todo-task-details-description'),
        taskDeadline = document.querySelector('.todo-task-details-deadline'),
        assignMemberInput = document.querySelector('.assign-member-for-task'),
        pickupbtn = document.getElementById('pickup-task-btn'),
        cancelBtn = document.getElementById('cancel-todo-task-details')

    todoTaskPopup.classList.add('active')
    taskName.innerText = taskDetails['task_name']
    taskPriority.innerText = taskDetails['priority']
    taskPriority.classList.add(taskDetails['priority'])
    taskDescription.innerText = taskDetails['description']
    taskDeadline.innerText = "Deadline : " + taskDetails['deadline'].split(' ')[0]

    assignMemberInput.addEventListener('keyup', (event) =>{
            if (event.keyCode === 13 && assignMemberInput.value != "") {
                assignMemberTodoTask(taskName.innerText, assignMemberInput.value)
            }
    })

    pickupbtn.addEventListener("click", () => {
        pickupTask(taskName.innerText)
    })
    cancelBtn.addEventListener('click', () => {
        todoTaskPopup.classList.remove('active')
    })
}
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
            fetch("http://localhost/public/groupmember/taskfeedback", {
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
        showConfirmationPopup()
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
            fetch("http://localhost/public/groupmember/taskfeedback", {
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
tasks.forEach(task => {
    task.addEventListener('click', event => {
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
            taskDetails = getTaskDetails(ongoingTasks, taskName)

            popped = true;
            showOngoingPopup(taskDetails)
            task.classList.remove('clicked');
            // taskDetails = getTaskDetails(ongoingTasks, taskName)
            // const todoTaskDetails = document.querySelector('.TO-DO-task-details')

            // let code = `<div class="top-bar">
            //                     <h3>${taskDetails['task_name']}</h3>
            //                     <p class="${taskDetails['priority']}">${taskDetails['priority']}</p>
            //                 </div>
            //                 <p class="task-details-description">${taskDetails['description']}</p>
            //                 <p class="task-details-deadline">Deadline : ${taskDetails['deadline'].split(' ')[0]}</p>

            //                 <div class="task-feedbacks">
            //                     <p class="incomming-feedbacks"></p>
            //                     <p class="outgoing-feedbacks"></p>
            //                     <form action="#" method="post">
            //                         <label for="feedbackMessage"></label>
            //                         <input type="text" name="feedbackMessage" placeholder="Send something ...">
            //                         <button type="submit"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></button>
            //                     </form>
            //                 </div>
            //                 <div class="buttons">
            //                     <a id="cancel-task-details" href="#">Cancel</a>
            //                     <a href="#">Finish</a>
            //                 </div>`
            
            // todoTaskDetails.innerHTML = code
            // todoTaskDetails.classList.add('active')
            
            // const cancelTodoTaskDetails = document.getElementById('cancel-task-details')
            // cancelTodoTaskDetails.addEventListener('click', () => todoTaskDetails.classList.remove('active'))
        }else if(position > 832 && position < 1054){
            popped = true;
            taskDetails = getTaskDetails(reviewTasks, taskName)
            
            showReviewPopup(taskDetails)
            task.classList.remove('clicked');
            // taskDetails = getTaskDetails(reviewTasks, taskName)
            // const todoTaskDetails = document.querySelector('.TO-DO-task-details')

            // let code = `<div class="top-bar">
            //                     <h3>${taskDetails['task_name']}</h3>
            //                     <p class="${taskDetails['priority']}">${taskDetails['priority']}</p>
            //                 </div>
            //                 <p class="task-details-description">${taskDetails['description']}</p>
            //                 <p class="task-details-deadline">Deadline : ${taskDetails['deadline'].split(' ')[0]}</p>

            //                 <div class="task-feedbacks">
            //                     <p class="incomming-feedbacks"></p>
            //                     <p class="outgoing-feedbacks"></p>
            //                     <form action="#" method="post">
            //                         <label for="feedbackMessage"></label>
            //                         <input type="text" name="feedbackMessage" placeholder="Send something ...">
            //                         <button type="submit"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></button>
            //                     </form>
            //                 </div>
            //                 <div class="buttons">
                                
            //                     <a href="#" style="margin-left: 100px">Continue</a>
            //                 </div>`
            
            // todoTaskDetails.innerHTML = code
            // todoTaskDetails.classList.add('active')
            
            // const cancelTodoTaskDetails = document.querySelector('.buttons a')
            // cancelTodoTaskDetails.addEventListener('click', () => todoTaskDetails.classList.remove('active'))
        }

        // console.log(taskDetails);

        task.classList.remove('clicked');
    })
})


// create task popup

const priority = document.querySelector('.priority'),
btns = document.querySelector('.finish-created-task'),
addTaskBtn = document.getElementById('add-task-btn'),
addTaskPopup = document.querySelector('.create-task-popup'),
cancelBtn = document.getElementById('cancel-task-btn'),
createTaskBtn = document.getElementById('create-task-btn'),
actualPriority = document.querySelector('.select-priority input');


addTaskBtn.addEventListener('click', () => {
    addTaskPopup.classList.add('active')
})

cancelBtn.addEventListener('click', () => {
    addTaskPopup.classList.remove('active')
})


// set group details
const groupName = document.getElementById('groupName');
const groupDescription = document.getElementById('groupDescription');
const groupStart = document.getElementById('groupStart');
const groupEnd = document.getElementById('groupEnd');

groupName.innerText = jsonData['groupDetails']['name']
groupDescription.innerText = jsonData['groupDetails']['description']
groupStart.innerHTML = "Start date : " + jsonData['groupDetails']['start_date'] + '<span>Deadline : ' + jsonData['groupDetails']['end_date'] + '</span>'


// set user profile
const profileImage = document.querySelector('.profile-image');
profileImage.src = jsonData['userDetails']

// set project name
const projectName = document.getElementById('projectName');
projectName.innerText = jsonData['projectDetails']

const notificationPopupBtn = document.querySelector('.notification-bell-btn')
const notificationPopup = document.querySelector('.notification-popup-container');
const notificationPopupCloseBtn = document.querySelector('.notification-popup-close-btn');
const notificationPopupContainer = document.querySelector('.notification-popup-container');
const container = document.querySelector('.container');

notificationPopupBtn.addEventListener('click', () => notificationPopup.classList.add('active'));
notificationPopupCloseBtn.addEventListener('click', () => notificationPopup.classList.remove('active'));
notificationPopup.addEventListener('click', () => notificationPopup.classList.remove('active'))
