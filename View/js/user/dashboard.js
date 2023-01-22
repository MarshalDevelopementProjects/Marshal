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

function createProjectDiv(obj) {
  const Project = document.createElement("a");
  Project.className = "project-card";
  Project.href = `http://localhost/public/user/project?id=${obj.id}`;
  Project.style.width = "300px";
  Project.style.height = "400px";
  Project.style.display = "inline-block";
  Project.style.textDecoration = "none";
  Project.style.color = "black";
  Project.style.margin = "10px";
  Project.style.padding = "20px";
  Project.style.boxShadow = "0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19)";
  Project.style.borderRadius = "10px";
  Project.style.backgroundColor = "#f2f2f2";
  
  Project.innerHTML = `
        <h3 style="width: 100%; text-align: center;">${obj.project_name}</h3>
        <div style="height: 150px; width: 100%; margin: 10px; background-image: url(${obj.image}); background-size: cover; background-position: center;"></div>
        <p style="width: 100%; text-align: center;"> Description : ${obj.description}</p>
        <p style="width: 100%; text-align: center;"> Start Date : ${obj.start_on}</p>
        <p style="width: 100%; text-align: center;"> End Date : ${obj.end_on}</p>
        `;
  
  ProjectContainerDiv.appendChild(Project);
}