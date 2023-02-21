console.log(jsonData);

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
                                    <p class="deadline">${task['deadline'].split(' ')[0]}</p>
                                </div>
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
                                <div class="bottom-task style="display:flex">
                                    <p class="deadline">${task['deadline'].split(' ')[0]}</p>
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
            }
            if(event.clientX - startX < 0){
                fetch("http://localhost/public/projectleader/rearangetask", {
                    withCredentials: true,
                    credentials: "include",
                    mode: "cors",
                    method: "POST",
                    body: JSON.stringify({
                        "task_name" : draggedTaskName.toString(),
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
                            <div class="bottom">
                                <input type="text" name="assignedMember" placeholder="Assign member ...">
                                <div class="buttons">
                                    <a id="cancel-task-details" href="#">Cancel</a>
                                    <a id="pickup-task-btn" href="#">PickUp</a>
                                </div>
                            </div>`
            
            todoTaskDetails.innerHTML = code
            todoTaskDetails.classList.add('active')

            // assign memeber feature 
            const assignMemberInput = document.querySelector('.bottom input');

            assignMemberInput.addEventListener('keyup', (event) =>{
                if (event.keyCode === 13) {
                    
                    fetch("http://localhost/public/projectleader/assigntask", {
                        withCredentials: true,
                        credentials: "include",
                        mode: "cors",
                        method: "POST",
                        body: JSON.stringify({
                            "task_name" : taskDetails['task_name'],
                            "member_username" : assignMemberInput.value
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
                }
            })

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

