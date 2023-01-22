const CreateProjectButton = document.getElementById("create-project-btn");
const LogOutButton = document.getElementById("log-out-btn");
const CreateProjectForm = document.getElementById("create-project-form");

const ProjectContainerDiv = document.getElementById("project-container-div");
const ModelContainerDiv = document.getElementById("model-container-div");
const CloseModelButton = document.getElementById("model-close-btn");

const OnLoad = async function () {
  // get the user projects from the backend
  try {
    let response = await fetch(
      "http://localhost/public/user/projects", 
      {
        withCredentials: true,
        credentials: "include",
        mode: "cors",
        method: "GET",
      }
    );

    // this is needed for the landing page
    let obj = await response.json();
    console.log(obj);

    if (response.ok) {
      if (obj.projects !== undefined) {
        obj.projects.forEach((element) => {
          createProjectDiv(element);
        });
      }
    }
   } catch (error) {
    console.error(error);
  }
};

function removeElementsFromProjectContainerDiv() {
  ProjectContainerDiv.innerHTML = "";
}

// open the model on click
CreateProjectButton.addEventListener("click", () => {
  console.log("This is the create-project button click event");
  ModelContainerDiv.classList.add("show-model");
});

CloseModelButton.addEventListener("click", () => {
  console.log("This is the create-project button click event");
  ModelContainerDiv.classList.remove("show-model");
  removeElementsFromProjectContainerDiv();
  OnLoad();
});

CreateProjectForm.addEventListener("submit", (event) => {
  event.preventDefault();
  const CreateProject = async function createProject() {
    let formData = new FormData(CreateProjectForm);
    let jsonFormData = JSON.stringify(Object.fromEntries(formData));
    console.log(jsonFormData);

    // have to perform validations on the form data
    try {
      let response = await fetch(
        "http://localhost/public/user/projects",
        {
          withCredentials: true,
          credentials: "include",
          mode: "cors",
          method: "POST",
          body: jsonFormData,
        }
      );

      let obj = await response.json();
      console.log(obj);

      if (response.ok) {
        console.log(obj);
        alert("New project is created");
        // reset the input fields
        CreateProjectForm.reset();
        // window.replace('./landing.html');
      } else {
        // check the errors
        alert("Project cannot be created");
      }
    } catch (error) {
      console.error(error);
    }
  };
  CreateProject();
});

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

// takes a js object as an argument
function createProjectDiv(obj) {
  const Project = document.createElement("div");
  Project.className = "project-card";

  Project.innerHTML = `
        <h3 style="width: 400px;">${obj.project_name}</h3>
        <div style="height: 150px; background-color: bisque; width: 100%; margin: 10px;">
                An image goes here
        </div>
        <p style="width: 400px;"> Description : ${obj.description}</p>
        <br>
        <p style="width: 400px;"> Start Date : ${obj.start_on}</p>
        <br>
        <p style="width: 400px;"> End Date : ${obj.end_on}</p>
        `;
  Project.addEventListener('click', function() {
      const PAGER_FOR_USER_PROJECTS = async function (obj) {
      let url = "http://localhost/public/user/project?id=" + obj.id;
      let response = await fetch(url, {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "GET",
            redirect: "follow",
            cache: "no-cache"
        }
      );
      if (response.ok) {
        let res = await response.json();
        window.location.replace(res.url);
      }
    }
    PAGER_FOR_USER_PROJECTS(obj);
  });
  ProjectContainerDiv.appendChild(Project);
}
