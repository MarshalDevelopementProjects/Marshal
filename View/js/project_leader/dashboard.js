const MessageForumLink = document.getElementById("message-forum-link");
MessageForumLink.setAttribute("href", `http://localhost/public/message/forum?project_id=${jsonData.project_id}`);

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

nextMonthBtn.addEventListener('click', function() {
    monthState += 1;

    monthText.innerHTML = months[(currentDate.getMonth() + monthState) % 12];

    if ((currentDate.getMonth() + monthState) % 12 == 0) {
        year += 1;
    }
    if (monthState == 12) {
        monthState = 0;
    }
    yearText.innerHTML = year;
    month = currentDate.getMonth() + monthState;

    daysTxt.innerHTML = renderDays(year, month, monthState);
    console.log(monthState);

})

previousMonthBtn.addEventListener('click', function() {

    if (currentDate.getMonth() + monthState == 0) {
        year -= 1;
    }
    if (monthState == 0) {
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
    for (var i = 0; i < 42; i++) {

        if (checkWeek == 0) {
            code += '<div class="days-line">'
        }
        if (dayNo > lastMonthEnd && dayStatus == 'inactive') {
            dayStatus = 'active';
            dayNo = 1;
        }
        if (dayNo > currentMonthEnd && dayStatus == 'active') {
            dayStatus = 'inactive';
            dayNo = 1;
        }

        if (dayStatus == 'active' && monthState == 0 && i == currentDate.getDate() + (lastMonthEnd - lastMonthStart)) {
            code += `<p class="day today ${dayStatus}">${dayNo}</p>`;
        } else if (monthState == 0 && i % 11 == 1 && dayStatus == 'active') {
            code += `<p class="day deadline ${dayStatus}">${dayNo}</p>`;
        } else {
            code += `<p class="day ${dayStatus}">${dayNo}</p>`;
        }

        dayNo += 1;
        checkWeek += 1;

        if (checkWeek == 7) {
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


console.log(jsonData)

// set tasks of the project


todoBoard = document.querySelector('.todo .tasks'),
    ongoingBoard = document.querySelector('.ongoing .tasks'),
    reviewBoard = document.querySelector('.review .tasks'),
    doneBoard = document.querySelector('.done .tasks');

todoTasksCode = "";

jsonData['todoTasks'].forEach(task => {
    todoTasksCode += `<div class="task" draggable="true">
                            <div class="top-task">
                                <h4>${task['task_name']}</h4>
                                <p class="priority-${task['priority']}">${task['priority']}</p>
                            </div>
                            <p class="task-description">${task['description']}</p>
                            <p class="deadline">${task['deadline'].split(' ')[0]}</p>
                        </div>`
})
todoBoard.innerHTML = todoTasksCode;

ongoingTasksCode = "";

jsonData['ongoingTasks'].forEach(task => {
    ongoingTasksCode += `<div class="task" draggable="true">
                            <div class="top-task">
                                <h4>${task['task_name']}</h4>
                                <p class="priority-${task['priority']}">${task['priority']}</p>
                            </div>
                            <p class="task-description">${task['description']}</p>
                            <p class="deadline">${task['deadline'].split(' ')[0]}</p>
                        </div>`
})
ongoingBoard.innerHTML = ongoingTasksCode;

reviewTasksCode = "";

jsonData['reviewTasks'].forEach(task => {
    reviewTasksCode += `<div class="task" draggable="true">
                            <div class="top-task">
                                <h4>${task['task_name']}</h4>
                                <p class="priority-${task['priority']}">${task['priority']}</p>
                            </div>
                            <p class="task-description">${task['description']}</p>
                            <p class="deadline">${task['deadline'].split(' ')[0]}</p>
                        </div>`
})
reviewBoard.innerHTML = reviewTasksCode;

doneTasksCode = "";

jsonData['doneTasks'].forEach(task => {
    doneTasksCode += `<div class="task" draggable="true">
                            <div class="top-task">
                                <h4>${task['task_name']}</h4>
                                <p class="priority">${task['priority']}</p>
                            </div>
                            <p class="task-description">${task['description']}</p>
                            <p class="deadline">${task['deadline'].split(' ')[0]}</p>
                        </div>`
})
doneBoard.innerHTML = doneTasksCode;


// drag and drop tasks

const tasks = document.querySelectorAll('.task');
const boards = document.querySelectorAll('.board');

var startX, endX;

tasks.forEach(task => {
    task.addEventListener('dragstart', (event) => {
        task.classList.add('dragging');
        startX = event.clientX;
    })

    task.addEventListener('dragend', (event) => {
        task.classList.remove('dragging');
        endX = event.clientX;
    })
})

boards.forEach(board => {
    board.addEventListener('dragover', event => {
        event.preventDefault();
        // const afterElement = getDragAfterElement(board, event.clientY);
        // console.log(board)

        const task = document.querySelector('.dragging');

        var dragDistance = event.clientX - startX;
        if (dragDistance > 0 && dragDistance < 350) {
            console.log(event.clientX - startX)

            var firstChild = board.firstChild
            if (firstChild) {
                var secondChild = firstChild.nextSibling;
                if (secondChild) {
                    board.insertBefore(task, secondChild)
                } else {
                    board.appendChild(task);
                }

            } else {
                board.appendChild(task);
            }

        }
    })
})

function getDragAfterElement(board, y) {
    const draggableElements = [...board.querySelectorAll('.task:not(.dragging)')]

    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect()
        const offset = y - box.top - box.height / 2
            // console.log(offset)
        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child }
        } else {
            return closest
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element
}







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