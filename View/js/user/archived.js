const archived_projects_btn1 = document.getElementById("archived-projects"),
    archived_projects_btn2 = document.getElementById("archived-projects-2")
    projects_container = document.querySelector(".projects"),
    archived_projects = document.querySelector(".archived_projects"),
    dashboardBtn = document.querySelector(".dashboard");

function onClickArchivedProjects() {
  projects_container.classList.add("remove");
  dashboardBtn.classList.remove("active");
  archived_projects_btn1.classList.add("active");
  archived_projects_btn2.classList.add("active");
  archivedPopupShow = true;
  console.log(archivedPopupShow);
}

archived_projects_btn1.addEventListener("click", onClickArchivedProjects);
archived_projects_btn2.addEventListener("click", onClickArchivedProjects);

dashboardBtn.addEventListener("click", function(){
    archivedPopupShow = false;
})

function getArchivedProjectsCode(){
    let code = ""

    jsonData['archived_projects'].forEach(project => {
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

archived_projects.innerHTML = getArchivedProjectsCode();
// console.log(getArchivedProjectsCode())

function isLeader(projects, id){

    let res;
    if(projects){
        projects.forEach(function(project){
    
            if(project.id == id){
                console.log(project.isLeader)
                res = project.isLeader;
            }
        });
    }
    return res
}

// Get all the project cards
const projectCards = document.querySelectorAll('.project-card');

projectCards.forEach(card => {
  const unarchivedBtn = card.querySelector('.unarchived-btn');
  const deleteBtn = card.querySelector('.delete-btn');
  
  console.log(isLeader(jsonData.archived_projects, unarchivedBtn.querySelector('i').getAttribute('value')))

//   console.log(unarchivedBtn.querySelector('i').getAttribute('value'))
 if(isLeader(jsonData.archived_projects, unarchivedBtn.querySelector('i').getAttribute('value'))) {
    unarchivedBtn.addEventListener('click', () => {

        fetch("http://localhost/public/user/unarchive", {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "POST",
            body: JSON.stringify({
                "id" : unarchivedBtn.querySelector('i').getAttribute('value')
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data)
            location.reload()
        })
        .catch((error) => {
            console.error(error)
        });
    
      });

      deleteBtn.addEventListener('click', () => {

        fetch("http://localhost/public/user/deleteproject", {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "POST",
            body: JSON.stringify({
                "id" : unarchivedBtn.querySelector('i').getAttribute('value')
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log(data)
            location.reload()
        })
        .catch((error) => {
            console.error(error)
        });
    
      })
 }
});