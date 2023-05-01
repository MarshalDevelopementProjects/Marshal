
function getTodoTasksCode(todoTasks){

    let todoTasksCode = "";
    if(todoTasks){
        todoTasks.forEach(task => {
            todoTasksCode += `<div class="task" draggable="true">
                                    <div class="top-task">
                                        <h4>${task['task_name']}</h4>
                                        <p class="priority-${task['priority']}">${task['priority']}</p>
                                    </div>
                                    <p class="task-description">${task['description']}</p>
                                    <div class="bottom-task" style="display:flex">
                                        <p class="deadline">${task['deadline'].split(' ')[0]}</p>
                                    </div>
                                </div>`
        })
    }
    return todoTasksCode;
}

// build search engine
const searchInput = document.querySelector('.search-box input'),
    taskBoards = document.querySelector('.task-boards'),
    searchResults = document.querySelector('.search-results');

function getTaskNames(tasks){
    var taskNames = [];
    
    tasks['todoTasks'].forEach(task =>{
        taskNames.push(task['task_name'])
    })
    tasks['ongoingTasks'].forEach(task =>{
        taskNames.push(task['task_name'])
    })
    tasks['reviewTasks'].forEach(task =>{
        taskNames.push(task['task_name'])
    })
    tasks['doneTasks'].forEach(task =>{
        taskNames.push(task['task_name'])
    })
    return taskNames
}

function getMatchedTaskNames(keyword){
    let tasks = []
    if(jsonData['tasks']){
        tasks = getTaskNames(jsonData['tasks'])
    }else{
        tasks = getTaskNames(jsonData['groupTasks'])
    }
    return tasks.filter(task => task.toLowerCase().startsWith(keyword.toLowerCase()))
}

function getMatchedTasks(keyword){
    let taskNames = getMatchedTaskNames(keyword)
    let matchedtasks = []

    let tasks = []
    if(jsonData['tasks']){
        tasks = jsonData['tasks']
    }else{
        tasks = jsonData['groupTasks']
    }
    
    matchedtasks.push(tasks['todoTasks'].filter(task => taskNames.includes(task['task_name'])))
    matchedtasks.push(tasks['ongoingTasks'].filter(task => taskNames.includes(task['task_name'])))
    matchedtasks.push(tasks['reviewTasks'].filter(task => taskNames.includes(task['task_name'])))
    matchedtasks.push(tasks['doneTasks'].filter(task => taskNames.includes(task['task_name'])))

    return matchedtasks
}

function getSearchResults(keyword){
    let code = ""
    getMatchedTasks(keyword).forEach(board => {
        code += getTodoTasksCode(board)
    })

    searchResults.innerHTML = code
}

searchInput.addEventListener('click', () => {
    taskBoards.classList.add('active');
    searchResults.classList.add('active');

    getSearchResults("")
})

taskBoards.addEventListener('click', () => {
    taskBoards.classList.remove('active');
    searchResults.classList.remove('active');
    searchResults.innerHTML = ""
    searchInput.value = ""
})

searchInput.addEventListener('input', () => {
    getSearchResults(searchInput.value)
})