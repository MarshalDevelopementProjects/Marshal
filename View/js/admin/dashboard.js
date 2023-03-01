console.log(jsonData);

const LogOutButton = document.getElementById("log-out-btn");
const countBlockUser = document.getElementById("numOfBlock");
const countActiveUser = document.getElementById("numOfActive");
const countAllUser = document.getElementById("numOfAll");
const adminName = document.getElementById("admin-user-name");

countBlockUser.innerText = `${jsonData.block_user_count}`;
countActiveUser.innerText = `${jsonData.active_user_count}`;
countAllUser.innerText = `${jsonData.all_user_count}`;
adminName.innerText = `${jsonData.admin_data.username}`;

tableRows = document.querySelector('.table-row');

function onLoad(){
    tableRowCode = ""
    jsonData.user_details.forEach(tableRow => {
        tableRowCode += `<tr class ="row">
                        <td class="people">
                            <img src="${tableRow['profile_picture']}" alt="">
                            <div class="people-de">
                                <h5 id="username">${tableRow['username']}</h5>
                            </div>
                        </td>
                        <td class="people-email">
                            <h5>${tableRow['email_address']}</h5>
                        </td>
                        <td class="joined_datetime">
                            <p>${tableRow['joined_datetime']}</p>
                        </td>
                        <td class="access">
                            <p>${tableRow['access']}</p>
                        </td>
                        <td class="status">`;
                        if (tableRow['user_state'] === "ONLINE") {
                          tableRowCode += `<i class="fa fa-circle green"></i>`;
                        } else {
                          tableRowCode += `<i class="fa fa-circle red"></i>`;
                        }
                        tableRowCode += `</td>
                                    </tr>`;
    })
    tableRows.innerHTML = tableRowCode
    AllUsersDiv.classList.add('active');
    allUserBtn.classList.add('active');
    getTableRowData();
}

LogOutButton.addEventListener("click", () => {
    fetch("http://localhost/public/admin/logout", {
        withCredentials: true,
        credentials: "include",
        mode: "cors",
        method: "POST",
    })
        .then((response) => {
            if (response.ok) {
                window.location.replace("http://localhost/public/admin/login");
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
        .catch((error) => console.error(error));
});

const ActiveUsersDiv = document.getElementById("active-users-div");

ActiveUsersDiv.addEventListener('click', async (event) => {
    let response = await fetch("http://localhost/public/admin/users/active", {
        withCredentials: true,
        credentials: "include",
        mode: "cors",
        method: "GET"
    });

    let data =  await response.json();
    if(response.ok) {
        tableRows.innerHTML = ''
        console.log(data);
        tableRowCode = ""
        data.active_users.forEach(tableRow => {
        tableRowCode += `<tr class ="row">
                        <td class="people">
                            <img src="${tableRow['profile_picture']}" alt="">
                            <div class="people-de">
                                <h5 id="username">${tableRow['username']}</h5>
                            </div>
                        </td>
                        <td class="people-email">
                            <h5>${tableRow['email_address']}</h5>
                        </td>
                        <td class="joined_datetime">
                            <p>${tableRow['joined_datetime']}</p>
                        </td>
                        <td class="access">
                            <p>${tableRow['access']}</p>
                        </td>
                        <td class="status">`;
                        if (tableRow['user_state'] === "ONLINE") {
                          tableRowCode += `<i class="fa fa-circle green"></i>`;
                        } else {
                          tableRowCode += `<i class="fa fa-circle red"></i>`;
                        }
                        tableRowCode += `</td>
                                    </tr>`;
    })
    tableRows.innerHTML = tableRowCode
    ActiveUsersDiv.classList.add('active');
    BlockedUsersDiv.classList.remove('active');
    AllUsersDiv.classList.remove('active');
    wrapper.classList.remove('active');
    userTable.classList.remove('active');
    addNewUserBtn.classList.remove('active');
    allUserBtn.classList.add('active');
    getTableRowData()
    }
    // alert(data.message);
});

const BlockedUsersDiv = document.getElementById("blocked-users-div");

BlockedUsersDiv.addEventListener('click', async (event) => {
    let response = await fetch("http://localhost/public/admin/users/blocked", {
        withCredentials: true,
        credentials: "include",
        mode: "cors",
        method: "GET"
    });

    let data =  await response.json();
    if(response.ok) {
        tableRows.innerHTML = ''
        console.log(data);
        tableRowCode = ""
        data.blocked_users.forEach(tableRow => {
        tableRowCode += `<tr class ="row">
                        <td class="people">
                            <img src="${tableRow['profile_picture']}" alt="">
                            <div class="people-de">
                                <h5 id="username">${tableRow['username']}</h5>
                            </div>
                        </td>
                        <td class="people-email">
                            <h5>${tableRow['email_address']}</h5>
                        </td>
                        <td class="joined_datetime">
                            <p>${tableRow['joined_datetime']}</p>
                        </td>
                        <td class="access">
                            <p>${tableRow['access']}</p>
                        </td>
                        <td class="status">`;
                        if (tableRow['user_state'] === "ONLINE") {
                          tableRowCode += `<i class="fa fa-circle green"></i>`;
                        } else {
                          tableRowCode += `<i class="fa fa-circle red"></i>`;
                        }
                        tableRowCode += `</td>
                                    </tr>`;
    })
    tableRows.innerHTML = tableRowCode
    BlockedUsersDiv.classList.add('active');
    AllUsersDiv.classList.remove('active');
    ActiveUsersDiv.classList.remove('active');
    wrapper.classList.remove('active');
    userTable.classList.remove('active');
    addNewUserBtn.classList.remove('active');
    allUserBtn.classList.add('active');
    getTableRowData()
    }
});


const AllUsersDiv = document.getElementById("all-users-div");

AllUsersDiv.addEventListener('click', async (event) => {
    let response = await fetch("http://localhost/public/admin/users/all", {
        withCredentials: true,
        credentials: "include",
        mode: "cors",
        method: "GET"
    });

    let data =  await response.json();
    if(response.ok) {
        tableRows.innerHTML = ''
        console.log(data.user_details);
        tableRowCode = ""
        data.user_details.forEach(tableRow => {
        tableRowCode += `<tr class ="row">
                        <td class="people">
                            <img src="${tableRow['profile_picture']}" alt="">
                            <div class="people-de">
                                <h5 id="username">${tableRow['username']}</h5>
                            </div>
                        </td>
                        <td class="people-email">
                            <h5>${tableRow['email_address']}</h5>
                        </td>
                        <td class="joined_datetime">
                            <p>${tableRow['joined_datetime']}</p>
                        </td>
                        <td class="access">
                            <p>${tableRow['access']}</p>
                        </td>
                        <td class="status">`;
                        if (tableRow['user_state'] === "ONLINE") {
                          tableRowCode += `<i class="fa fa-circle green"></i>`;
                        } else {
                          tableRowCode += `<i class="fa fa-circle red"></i>`;
                        }
                        tableRowCode += `</td>
                                    </tr>`;
    })
    tableRows.innerHTML = tableRowCode
    AllUsersDiv.classList.add('active');
    BlockedUsersDiv.classList.remove('active');
    ActiveUsersDiv.classList.remove('active');
    wrapper.classList.remove('active');
    userTable.classList.remove('active');
    addNewUserBtn.classList.remove('active');
    allUserBtn.classList.add('active');
    getTableRowData()
    }
});

const addNewUserBtn = document.querySelector("#add-New-User");
const allUserBtn = document.querySelector("#all-Users");
const userTable = document.getElementById("user-table");
const wrapper = document.querySelector(".wrapper");

addNewUserBtn.addEventListener('click', ()=>{
    userTable.classList.add('active');
    wrapper.classList.add('active');
    addNewUserBtn.classList.add('active');
    allUserBtn.classList.remove('active');
    AllUsersDiv.classList.remove('active');
    
});

allUserBtn.addEventListener('click', async (event) => {
    let response = await fetch("http://localhost/public/admin/users/all", {
        withCredentials: true,
        credentials: "include",
        mode: "cors",
        method: "GET"
    });

    let data =  await response.json();
    if(response.ok) {
        tableRows.innerHTML = ''
        console.log(data.user_details);
        tableRowCode = ""
        data.user_details.forEach(tableRow => {
        tableRowCode += `<tr class ="row">
                        <td class="people">
                            <img src="${tableRow['profile_picture']}" alt="">
                            <div class="people-de">
                                <h5 id="username">${tableRow['username']}</h5>
                            </div>
                        </td>
                        <td class="people-email">
                            <h5>${tableRow['email_address']}</h5>
                        </td>
                        <td class="joined_datetime">
                            <p>${tableRow['joined_datetime']}</p>
                        </td>
                        <td class="access">
                            <p>${tableRow['access']}</p>
                        </td>
                        <td class="status">`;
                        if (tableRow['user_state'] === "ONLINE") {
                          tableRowCode += `<i class="fa fa-circle green"></i>`;
                        } else {
                          tableRowCode += `<i class="fa fa-circle red"></i>`;
                        }
                        tableRowCode += `</td>
                                    </tr>`;
    })
    tableRows.innerHTML = tableRowCode
    AllUsersDiv.classList.add('active');
    allUserBtn.classList.add('active');
    BlockedUsersDiv.classList.remove('active');
    ActiveUsersDiv.classList.remove('active');
    wrapper.classList.remove('active');
    userTable.classList.remove('active');
    addNewUserBtn.classList.remove('active');
    getTableRowData()
    }
});

const AddNewUserFrom = document.getElementById("add-New-User-Form");
const MessageBox = document.querySelector(".msg");

AddNewUserFrom.addEventListener('submit', async function(event) {
    event.preventDefault();
    let formData = new FormData(AddNewUserFrom);
    let jsonFormData = JSON.stringify(Object.fromEntries(formData));
    console.log(jsonFormData);
    try {
        let response = await fetch("http://localhost/public/admin/users/addnewuser", {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "POST",
            body: formData
        });

        let returnData = await response.json();
        console.log(returnData);
        if (response.ok) {
            for (let i = 0; i < AddNewUserFrom.elements.length; i++) {
                AddNewUserFrom.elements[i].value = "";
            }
            jsonData.user_info = returnData.user_info;
            location.reload();
            exit; 
        }
        //alert(returnData.errors);
        errorsAlert();
        MessageBox.innerText = `${returnData.errors}`;
        document.querySelector(".alert").style.cssText = "background-color: #ffe1e3; border-left: 8px solid #fe475c;";
        document.querySelector(".fa-exclamation-circle").style.cssText = "color: #fe475c;";
        document.querySelector(".msg").style.cssText = "color: #ec7a8b;";
        document.querySelector(".close-btn").style.cssText = "background-color: #ff99a4;";
        document.querySelector(".fa-times").style.cssText = "color: #fc4a57;";
    } catch (error) {
        // alert(error.message);
        console.log(error);
    }
});

const FormCancelBtn = document.getElementById("cancel-btn");
FormCancelBtn.addEventListener('click',()=>{
    for (let i = 0; i < AddNewUserFrom.elements.length; i++) {
        AddNewUserFrom.elements[i].value = "";
    }
    location.reload();
});

//Errors alert
function errorsAlert(){
    document.querySelector('.alert').classList.add('show');
    document.querySelector('.alert').classList.remove('hide');
    document.querySelector('.alert').classList.add('showAlert');
    setTimeout(() => {
    document.querySelector('.alert').classList.remove('show');
    document.querySelector('.alert').classList.add('hide');
    }, 5000);
    document.querySelector('.close-btn').addEventListener('click', () => {
    document.querySelector('.alert').classList.remove('show');
    document.querySelector('.alert').classList.add('hide');
    });
}

const user_name = document.getElementById('w2-name');
const user_phone = document.getElementById('w2-phone');
const user_username = document.getElementById('w2-username');
const user_email = document.getElementById('w2-email');
const user_date = document.getElementById('w2-date');
const user_position = document.getElementById('w2-position');
const user_profile = document.getElementById('w2-profile');
const user_blockbtn = document.querySelector('.w2-block-btn');
const user_unblockbtn = document.querySelector('.w2-unblock-btn');
const wrapper2 = document.querySelector('.wrapper-2');
const block_form = document.querySelector('#block-form');
const unBlock_form = document.querySelector('#unBlock-form');

function getTableRowData(){
    const tableRowsData = document.getElementsByClassName("row");
    for (var i = 0; i < tableRowsData.length; i++) {
        tableRowsData[i].addEventListener("click", function() {
            var name = this.getElementsByClassName("people-de")[0].getElementsByTagName("h5")[0].innerHTML;
            $array = jsonData.user_details;
            for (let index = 0; index < $array.length; index++) {
                if(name === jsonData.user_details[index].username){
                    user_username.innerText = jsonData.user_details[index].username;
                    user_name.innerText = jsonData.user_details[index].first_name +' '+ jsonData.user_details[index].last_name;
                    user_phone.innerText = jsonData.user_details[index].phone_number;
                    user_email.innerText = jsonData.user_details[index].email_address;
                    user_date.innerText = jsonData.user_details[index].joined_datetime;
                    user_position.innerText = jsonData.user_details[index].position;
                    user_profile.setAttribute('src',jsonData.user_details[index].profile_picture);
                    wrapper2.classList.add('active');
                    if(jsonData.user_details[index].access == 'ENABLED'){
                        unBlock_form.style.display = 'none';
                        block_form.style.display = 'block';
                    }else{
                        block_form.style.display = 'none';
                        unBlock_form.style.display = 'block';
                    }
                    block_form.addEventListener('submit', async function(event) {
                        event.preventDefault();
                        let requestData = {key:'username',value:jsonData.user_details[index].username};
                        let jsonFormData = JSON.stringify(requestData);
                        console.log(jsonFormData);
                        try {
                            let response = await fetch("http://localhost/public/admin/users/userblock", {
                                withCredentials: true,
                                credentials: "include",
                                mode: "cors",
                                method: "PUT",
                                body: jsonFormData
                            });
                    
                            let returnData = await response.json();
                            console.log(returnData);
                            if (response.ok) {
                            MessageBox.innerText = `${returnData.message}`;
                            document.querySelector(".alert").style.cssText = "background-color: #c5f3d7; border-left: 8px solid #2dd670;";
                            document.querySelector(".msg").style.cssText = "color: #5fb082;";
                            document.querySelector(".close-btn").style.cssText = "background-color:#94eab9;";
                            document.querySelector(".fas").style.cssText = "color: #21ab5e;";
                            var icon = document.querySelector(".fa-exclamation-circle") 
                            icon.classList.add('fas','fa-check-circle');
                            icon.classList.remove('fa-exclamation-circle')
                            document.querySelector(".fa-times").style.cssText = "color: #21ab5e;";
                            errorsAlert();
                            setTimeout(function() {
                                location.reload();
                              }, 1000);
                            }
                            
                        } catch (error) {
                            alert(error.message);
                            console.error(error);
                        }
                    });

                    unBlock_form.addEventListener('submit', async function(event) {
                        event.preventDefault();
                        let requestData = {key:'username',value:jsonData.user_details[index].username};
                        let jsonFormData = JSON.stringify(requestData);
                        console.log(jsonFormData);
                        try {
                            let response = await fetch("http://localhost/public/admin/users/userunblock", {
                                withCredentials: true,
                                credentials: "include",
                                mode: "cors",
                                method: "PUT",
                                body: jsonFormData
                            });
                    
                            let returnData = await response.json();
                            console.log(returnData);
                            if (response.ok) {
                                MessageBox.innerText = `${returnData.message}`;
                                document.querySelector(".alert").style.cssText = "background-color: #c5f3d7; border-left: 8px solid #2dd670;";
                                document.querySelector(".msg").style.cssText = "color: #5fb082;";
                                document.querySelector(".close-btn").style.cssText = "background-color:#94eab9;";
                                document.querySelector(".fas").style.cssText = "color: #21ab5e;";
                                var icon = document.querySelector(".fa-exclamation-circle") 
                                icon.classList.add('fas','fa-check-circle');
                                icon.classList.remove('fa-exclamation-circle')
                                document.querySelector(".fa-times").style.cssText = "color: #21ab5e;";
                                errorsAlert();
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            }

                        } catch (error) {
                            alert(error.message);
                            console.error(error);
                        }
                    });
                }
            }
        });
    }
}

function searchUser() {
    let input = document.getElementById("search").value.toLowerCase();
    console.log(input);
    let rows = document.querySelectorAll(".row");
    rows.forEach(row => {
        console.log(row.querySelector("#username").textContent.toLowerCase().indexOf(input) > -1);
      if (row.querySelector("#username").textContent.toLowerCase().indexOf(input) > -1 ){
        row.style.display = "";
      } else {
        row.style.display = "none";
        userNotFound();
      }
    });
  }
  document.getElementById("search").addEventListener("keyup", searchUser);
  
  function userNotFound(){
    const tableBody = document.getElementById("user-table");
    console.log(tableBody);
    const row = tableBody.insertRow();
    row.id = "user-not-found-row";
    const cell = row.insertCell(0);
    cell.colSpan = 6;
    cell.style.textAlign = "center";
    cell.innerHTML = "User not found.";
  }
  