
// set tasks of the project


todoBoard = document.querySelector('.todo .tasks'),
ongoingBoard = document.querySelector('.ongoing .tasks'),
reviewBoard = document.querySelector('.review .tasks'),
doneBoard = document.querySelector('.done .tasks');

var todoTasks = jsonData['todoTasks'];
var ongoingTasks = jsonData['ongoingTasks'];
var reviewTasks = jsonData['reviewTasks'];
var doneTasks = jsonData['doneTasks'];


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
                                <p class="deadline">${task['deadline'].split(' ')[0]}</p>
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
                                <p class="deadline">${task['deadline'].split(' ')[0]}</p>
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
                                <p class="deadline">${task['deadline'].split(' ')[0]}</p>
                            </div>`
    })
}
reviewBoard.innerHTML = reviewTasksCode;

var doneTasksCode = "";

if(doneTasks){
    doneTasks.forEach(task => {
        doneTasksCode += `<div class="task" draggable="true">
                                <div class="top-task">
                                    <h4>${task['task_name']}</h4>
                                    <p class="priority">${task['priority']}</p>
                                </div>
                                <p class="task-description">${task['description']}</p>
                                <p class="deadline">${task['deadline'].split(' ')[0]}</p>
                            </div>`
    })
}
doneBoard.innerHTML = doneTasksCode;

const getTaskDetails = (boardName, taskName) => {
    return boardName.find(element => element.task_name === taskName)
}

// drag and drop tasks

const tasks = document.querySelectorAll('.task');
const boards = document.querySelectorAll('.tasks');

var startX, endX;
// member does not allow to drag all tasks anywhere on anytime, they have restrictions
var hasAccess = true;

var oldBoard, newBoard;

tasks.forEach(task => {
    task.addEventListener('dragstart', (event)=>{
        task.classList.add('dragging');
        startX = event.clientX;

        oldBoard = task.parentNode.parentNode.className.split(' ')[0]
        if(oldBoard === "review" || oldBoard === "done") {
            hasAccess = false;
        }else{
            hasAccess = true;
        }
    })

    task.addEventListener('dragend', (event)=>{
        task.classList.remove('dragging');
        endX = event.clientX;

        newBoard = task.parentNode.parentNode.className.split(' ')[0].toUpperCase()

        if(newBoard != oldBoard.toUpperCase()){
            
            let draggedTaskName = task.firstElementChild.firstElementChild.textContent;
            
            fetch("http://localhost/public/projectmember/pickuptask", {
                withCredentials: true,
                credentials: "include",
                mode: "cors",
                method: "POST",
                body: JSON.stringify({
                    "task_name" : draggedTaskName.toString(),
                    "state" : newBoard
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
            })
            .catch((error) => {
                console.error(error)
            });
        }
                
    })
})

boards.forEach(board => {

    board.addEventListener('dragover', event => {
        event.preventDefault();

        const task = document.querySelector('.dragging');

        var dragDistance = event.clientX - startX;
        if(hasAccess){
            if(dragDistance > 0 && dragDistance < 350){
                // console.log(event.clientX - startX)

                var firstChild = board.firstChild
                if(firstChild){
                    // var secondChild = firstChild.nextSibling;
                    // console.log(firstChild)
                    
                    board.insertBefore(task, firstChild)
                    // board.appendChild(task);
                }else{
                    board.appendChild(task);
                }
                
            }
        }
        console.log(hasAccess)
    })
})

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
                                <a href="#">PickUp</a>
                            </div>`
            
            todoTaskDetails.innerHTML = code
            todoTaskDetails.classList.add('active')
            
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
                                <a href="#">Finish</a>
                            </div>`
            
            todoTaskDetails.innerHTML = code
            todoTaskDetails.classList.add('active')
            
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
                                
                                <a href="#" style="margin-left: 100px">Continue</a>
                            </div>`
            
            todoTaskDetails.innerHTML = code
            todoTaskDetails.classList.add('active')
            
            const cancelTodoTaskDetails = document.querySelector('.buttons a')
            cancelTodoTaskDetails.addEventListener('click', () => todoTaskDetails.classList.remove('active'))
        }else if(position > 1102 && position < 1324){
            taskDetails = getTaskDetails(doneTasks, taskName)
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
                                    <input disabled type="text" name="feedbackMessage" placeholder="Send something ...">
                                    <button disabled type="submit"><i class="fa fa-chevron-circle-right" aria-hidden="true"></i></button>
                                </form>
                            </div>
                            <div class="buttons">
                                
                                <a href="#" style="margin-left: 100px">Continue</a>
                            </div>`
            
            todoTaskDetails.innerHTML = code
            todoTaskDetails.classList.add('active')
            
            const cancelTodoTaskDetails = document.querySelector('.buttons a')
            cancelTodoTaskDetails.addEventListener('click', () => todoTaskDetails.classList.remove('active'))
        }

        console.log(taskDetails);

        task.classList.remove('clicked');
    })
})











// calendor 

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


const renderCalendar = (year, month) => {
    // getting last date of month
    // let lastDateOfMonth = new Date(year, currentDate.getMonth() + 1, 0).getDate();

    monthText.innerHTML = months[month];
    yearText.innerHTML = year;

    daysTxt.innerHTML = renderDays(year, month, monthState);
};
renderCalendar(year, month);

const LogOutButton = document.getElementById("log-out-btn");

// console.log(jsonData);







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


