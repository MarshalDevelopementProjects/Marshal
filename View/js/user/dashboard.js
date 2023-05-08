console.log(jsonData)

let archivedPopupShow = false;

const projects = document.querySelector('.projects');

function getProjectsCode(projects){
    // get ongoing tasks to an array 
    var ongoingTasks = {};
    var members = {};
    
    projects.forEach(project => {
        ongoingTasks[project['id']] = [];
        members[project['id']] = [];

        // get tasks
        if(project.tasks.length){
            for(let i = 0; i<4; i++){
                if(project.tasks[i] == undefined){
                    ongoingTasks[project['id']].push(" ");
                }else{
                    ongoingTasks[project['id']].push(project.tasks[i]['task_name']);
                }
            }
        }else{
            ongoingTasks[project['id']].push("There is nothing onging. Create and pickup new one.");
            for(let i = 1; i<4; i++){
                ongoingTasks[project['id']].push(" ");
            }
        }

        // get member profile details
        for(let i = 0; i <5; i++){
            if(project.memberProfiles[i] == undefined){
                members[project['id']].push('src="/View/images/Picture1.png" style="display: none;"');
            }else{
                members[project['id']].push('src=' + project.memberProfiles[i]['profile_picture']);
            }
        }
    })

    // console.log(typeof(jsonData['projects'][0]['memberProfiles'][0]['profile_picture']))
    projectCardsCode = ""
    projects.forEach(project => {
        projectCardsCode += `<div class="project-card">
                                <a href="http://localhost/public/user/project?id=${project['id']}" class="clickable-project">
                                    <p class="project-field ">${project['field']}</p>
                                <h3 class="project-name">${project['project_name']}</h3>

                                <div class="tasks">
                                    <p>Ongoing Work</p>
                                    <ul class="task-list">
                                        <li>${ongoingTasks[project['id']][0]}</li>
                                        <li>${ongoingTasks[project['id']][1]}</li>
                                        <li>${ongoingTasks[project['id']][2]}</li>
                                        <li>${ongoingTasks[project['id']][3]}</li>
                                        
                                    </ul>
                                </div>
                                <p class="team">Team</p>
                                <div class="card-bottom">
                                    <div class="member-images">
                                        <img class="image first" ${members[project['id']][0]} alt="Picture1">
                                        <img class="image rest1" ${members[project['id']][1]} alt="Picture2">
                                        <img class="image rest2" ${members[project['id']][2]} alt="Picture3">
                                        <img class="image rest3" ${members[project['id']][3]} alt="Picture4">
                                        <img class="image rest4" ${members[project['id']][4]} alt="Picture5">
                                    </div>
                                    <!-- <button type="submit">Get Info</button> -->
                                    
                                </div>
                                </a>
                            </div>`
    })
    return projectCardsCode
}

function ArchivedProjectsCode(projects){
    console.log(projects)
    let code = ""

    projects.forEach(project => {
        var members = [];

        for(let i = 0; i <5; i++){
            if(project['memberProfiles'][i] == undefined){
                members.push('src="/View/images/Picture1.png" style="display: none;"');
            }else{
                members.push('src=' + project['memberProfiles'][i]['profile_picture']);
            }
        }

        code += `<div class="project-card">
                                            
                        <p class="project-field ">${project['field']}</p>
                    <h3 class="project-name">${project['project_name']}</h3>

                    <div class="tasks">
                        <div class="unarchived-delete-btns">
                            <div class="unarchived-btn">
                                <i value="${project['id']}" class="fa fa-archive" aria-hidden="true"></i>
                            </div>
                            <div class="delete-btn">
                                <i value="${project['id']}" class="fa fa-trash" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                    <p class="team">Team</p>
                    <div class="card-bottom">
                        <div class="member-images">
                            <img class="image first" ${members[0]} alt="Picture1">
                            <img class="image rest1" ${members[1]} alt="Picture2">
                            <img class="image rest2" ${members[2]} alt="Picture3">
                            <img class="image rest3" ${members[3]} alt="Picture4">
                            <img class="image rest4" ${members[4]} alt="Picture5">
                        </div>
                        
                    </div>
                    
                </div>`
    });

    return code;
}

// this onLoad function receive notifications and load page data
function onLoad(){
    // load project data

    let projectCardsCode = getProjectsCode(jsonData['projects'])

    projects.innerHTML = projectCardsCode

}

// Add profile picture
const profilePicture = document.querySelector('.profile-image');
profilePicture.src = jsonData['profile'];

// build search engine for projects

const searchInput = document.querySelector('.search-box input');

function getProjectNames(projects){
    var projectNames = [];
    projects.forEach(project => {
        projectNames.push(project['project_name'])
    })
    return projectNames
}

function getMatchedProjectNames(keyword, t_projects){
    let projects = getProjectNames(t_projects)
    return projects.filter(project => project.toLowerCase().startsWith(keyword.toLowerCase()))
}

function getMatchedProjects(keyword, projects){
    let projectNames = getMatchedProjectNames(keyword, projects)
    return projects.filter(project => projectNames.includes(project['project_name']))
}


    searchInput.addEventListener('input', () =>{

        if(archivedPopupShow){
            if(searchInput.value == ""){
                archived_projects.innerHTML = ArchivedProjectsCode(jsonData.archived_projects)
            }else{
                console.log(getMatchedProjects(searchInput.value, jsonData.archived_projects))
                let code = ArchivedProjectsCode(getMatchedProjects(searchInput.value, jsonData.archived_projects))
                
                archived_projects.innerHTML = code
            }
        }else{
            if(searchInput.value == ""){
                onLoad()
            }else{
                console.log(getMatchedProjects(searchInput.value, jsonData.projects))
                let code = getProjectsCode(getMatchedProjects(searchInput.value, jsonData.projects))
            
                projects.innerHTML = code
            }
        }
        
        
    })



/*------------------------------------------------------*/ 
const Project = document.querySelector('.projects')
const middle_ = document.querySelector('.middle-icon');
const middle_2 = document.querySelector('.middle-icon2');

middle_.addEventListener('click', function(){
    setTimeout(function() {
        Project.classList.add('active');
      }, 1000); // wait 1 second (1000 milliseconds)   
});

middle_2.addEventListener('click', function(){
    setTimeout(function() {
        Project.classList.remove('active');
      }, 1000); // wait 1 second (1000 milliseconds)   
});
/*------------------------------------------------------*/ 
