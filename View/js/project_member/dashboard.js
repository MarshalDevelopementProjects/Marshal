
/** 
 * @jsonData {array} : It returns all details that relevant to the page
 * From below lines of code,
 *      get the details (all tasks related to the project) and divide them into categories based on the status of the task
 *      then sort based on the priority of the task
*/

todoBoard = document.querySelector('.todo .tasks'),
ongoingBoard = document.querySelector('.ongoing .tasks'),
reviewBoard = document.querySelector('.review .tasks'),
doneBoard = document.querySelector('.done .tasks');

var todoTasks = jsonData['todoTasks'];
var ongoingTasks = jsonData['ongoingTasks'];
var reviewTasks = jsonData['reviewTasks'];
var doneTasks = jsonData['doneTasks'];

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
        if(task['memberId'] != task['userId']){
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
        if(task['memberId'] != task['userId']){
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
        doneTasksCode += `<div class="task" draggable="true"${access}>
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

/**
 * When tasks click , it must show details of it
 */
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
            const todoTaskDetails = document.querySelector('.TO-DO-task-details')

            let code = `<div class="top-bar">
                                <h3>${taskDetails['task_name']}</h3>
                                <p class="${taskDetails['priority']}">${taskDetails['priority']}</p>
                            </div>
                            <p class="task-details-description">${taskDetails['description']}</p>
                            <p class="task-details-deadline">Deadline : ${taskDetails['deadline'].split(' ')[0]}</p>

                            <div class="task-feedbacks">
                                <p class="incomming-feedbacks"></p>
                                <p class="outgoing-feedbacks"></p>
                                <form action="#" method="post">
                                    <label for="feedbackMessage"></label>
                                    <input type="text" name="feedbackMessage" placeholder="Send something ..." disabled>
                                    <button disabled type="submit"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></button>
                                </form>
                            </div>
                            <div class="buttons">
                                <a id="cancel-task-details" href="#">Cancel</a>
                                <a id="pickup-task-btn" href="#">PickUp</a>
                            </div>`
            
            todoTaskDetails.innerHTML = code
            todoTaskDetails.classList.add('active')

            const pickupTaskBtn = document.getElementById('pickup-task-btn')
            pickupTaskBtn.addEventListener('click', () => {
                let draggedTaskName = task.firstElementChild.firstElementChild.textContent;
                let newBoard = task.parentNode.parentNode.className.split(' ')[0].toUpperCase()

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
                    todoTaskDetails.classList.remove('active')
                    location.reload();
                })
                .catch((error) => {
                    console.error(error)
                });
            })
            
            const cancelTodoTaskDetails = document.getElementById('cancel-task-details')
            cancelTodoTaskDetails.addEventListener('click', () => todoTaskDetails.classList.remove('active'))

        }else if(position > 562 && position < 784){
            taskDetails = getTaskDetails(ongoingTasks, taskName)
            const todoTaskDetails = document.querySelector('.TO-DO-task-details')

            let code = `<div class="top-bar">
                                <h3>${taskDetails['task_name']}</h3>
                                <p class="${taskDetails['priority']}">${taskDetails['priority']}</p>
                            </div>
                            <p class="task-details-description">${taskDetails['description']}</p>
                            <p class="task-details-deadline">Deadline : ${taskDetails['deadline'].split(' ')[0]}</p>

                            <div class="task-feedbacks">
                                <p class="incomming-feedbacks"></p>
                                <p class="outgoing-feedbacks"></p>
                                <form action="#" method="post">
                                    <label for="feedbackMessage"></label>
                                    <input type="text" name="feedbackMessage" placeholder="Send something ...">
                                    <button type="submit"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></button>
                                </form>
                            </div>
                            <div class="buttons">
                                <a id="cancel-task-details" href="#">Cancel</a>
                                <p id="finishTaskBtn">Finish</p>
                            </div>`
            
            todoTaskDetails.innerHTML = code
            todoTaskDetails.classList.add('active')

            const finishBtn = document.querySelector('#finishTaskBtn')
            const confirmationPopup = document.querySelector('.confirmation-popup')
            const confirmationPopupCloseBtn = document.querySelector('.confirmation-popup .close-area i')

            finishBtn.addEventListener('click', () => confirmationPopup.classList.add('active'))

            const confirmationMessage = document.getElementById('confirmationMessage')
            const sendConfirmation = document.querySelector('.confirmation-popup .input-area button')
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
                console.log(message)
            })

            console.log(taskName)
            if(message != null){
                console.log("done")
                sendConfirmation.addEventListener('click', () => {
                    sendConfirmationFunction(taskName, message, formattedDate, time)
                    console.log("done")
                })
                confirmationMessage.addEventListener('keyup', (event) =>{
                    if(event.keyCode === 13){
                        sendConfirmationFunction(taskName, message, formattedDate, time)
                    }
                })
            }


            // confirmationPopup.classList.add('active')
            confirmationPopupCloseBtn.addEventListener('click', () => {
                confirmationPopup.classList.remove('active')
                location.reload();
            })
            
            const cancelTodoTaskDetails = document.getElementById('cancel-task-details')
            cancelTodoTaskDetails.addEventListener('click', () => todoTaskDetails.classList.remove('active'))
        }else if(position > 832 && position < 1054){
            taskDetails = getTaskDetails(reviewTasks, taskName)
            const todoTaskDetails = document.querySelector('.TO-DO-task-details')

            let code = `<div class="top-bar">
                                <h3>${taskDetails['task_name']}</h3>
                                <p class="${taskDetails['priority']}">${taskDetails['priority']}</p>
                            </div>
                            <p class="task-details-description">${taskDetails['description']}</p>
                            <p class="task-details-deadline">Deadline : ${taskDetails['deadline'].split(' ')[0]} <span>Completed at : ${taskDetails['completeTime']}</span></p>

                            <div class="task-feedbacks">
                                <p class="incomming-feedbacks"></p>
                                <p class="outgoing-feedbacks"></p>
                                <form action="#" method="post">
                                    <label for="feedbackMessage"></label>
                                    <input type="text" name="feedbackMessage" placeholder="Send something ...">
                                    <button type="submit"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></button>
                                </form>
                            </div>
                            <div class="buttons">
                                <p id="completedMessage">Confirmation message : <span>${taskDetails['confirmationMessage']}</span></p>
                                <p id="continueBtn" style="margin-left: 100px">Continue</p>
                            </div>`
            
            todoTaskDetails.innerHTML = code
            todoTaskDetails.classList.add('active')
            
            const cancelTodoTaskDetails = document.querySelector('#continueBtn')
            cancelTodoTaskDetails.addEventListener('click', () => todoTaskDetails.classList.remove('active'))
        }

        console.log(taskDetails);

        task.classList.remove('clicked');
    })
})











// calendor 
/**
 * 
 * @param {*} month Current month
 * @param {*} year Current year
 * @returns the date of last monday of previous month
 */
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

nextMonthBtn.addEventListener('click', function(){
    monthState += 1;

    monthText.innerHTML = months[(currentDate.getMonth() + monthState) % 12];
    
    if((currentDate.getMonth() + monthState) % 12 == 0){
        year += 1;
    }
    if(monthState == 12){
        monthState = 0;
    }
    yearText.innerHTML = year;
    month = currentDate.getMonth() + monthState;

    daysTxt.innerHTML = renderDays(year, month, monthState);
    console.log(monthState);

})

previousMonthBtn.addEventListener('click', function(){

    if(currentDate.getMonth() + monthState == 0){
        year -= 1;
    }
    if(monthState == 0){
        monthState = 12;
    }
    monthState -= 1;
    console.log(monthState);

    monthText.innerHTML = months[(currentDate.getMonth() + monthState) % 12];

    yearText.innerHTML = year;
    month = currentDate.getMonth() + monthState;

    daysTxt.innerHTML = renderDays(year, month, monthState);
})
/**
 * Show the calendar
 * @param {*} year 
 * @param {*} month 
 * @param {*} monthState 
 * @returns 
 */
const renderDays = (year, month, monthState) => {

    var checkWeek = 0;
    var dayStatus = 'inactive';
    var lastMonthStart = lastMondayOfMonth(month, year);

    var lastMonthEnd = new Date(year, currentDate.getMonth() + monthState, 0).getDate();
    var currentMonthEnd = new Date(year, currentDate.getMonth() + 1 + monthState, 0).getDate();
    var dayNo = lastMonthStart;
    // console.log(lastMonthStart)

    var code = "";
    for(var i=0; i<42; i++){

        if(checkWeek == 0){
            code += '<div class="days-line">'
        }
        if(dayNo > lastMonthEnd && dayStatus == 'inactive'){
            dayStatus = 'active';
            dayNo = 1;
        }
        if(dayNo > currentMonthEnd && dayStatus == 'active'){
            dayStatus = 'inactive';
            dayNo = 1;
        }

        if(dayStatus == 'active' && monthState == 0 && i == currentDate.getDate() + (lastMonthEnd - lastMonthStart)){
            code += `<p class="day today ${dayStatus}">${dayNo}</p>`;
        }else if(monthState == 0 && i %11 == 1 && dayStatus == 'active'){
            code += `<p class="day deadline ${dayStatus}">${dayNo}</p>`;
        }
        else{
            code += `<p class="day ${dayStatus}">${dayNo}</p>`;
        }
        
        dayNo += 1;
        checkWeek += 1;

        if(checkWeek == 7){
            code += '</div>';
            checkWeek = 0;
        }
    }
    return code;
}

/**
 * Filled up the calendar details
 * @param {*} year 
 * @param {*} month 
 */
const renderCalendar = (year, month) => {

    monthText.innerHTML = months[month];
    yearText.innerHTML = year;

    daysTxt.innerHTML = renderDays(year, month, monthState);
};
renderCalendar(year, month);




const LogOutButton = document.getElementById("log-out-btn");

LogOutButton.addEventListener("click", () => {
  fetch("http://localhost/public/user/logout", {
    withCredentials: true,
    credentials: "include",
    mode: "cors",
    method: "POST",
  })
    .then((response) => {
      if (response.ok) {
        window.location.replace("http://localhost/public/user/login");
        return;
      }
      if (!response.ok) {
        response.json();
      }
    })
    .then((data) => {
      if (data.message != undefined && data.message != undefined) {
        alert(data.message);
      } else {
        alert(data.message);
      }
    })
    .catch((error) => {
      console.error(error)
    });
});


