console.log(jsonData);

const LogOutButton = document.getElementById("log-out-btn");
const countBlockUser = document.getElementById("numOfBlock");
const countActiveUser = document.getElementById("numOfActive");
const countAllUser = document.getElementById("numOfAll");

countBlockUser.innerText = `${jsonData.block_user_count}`;
countActiveUser.innerText = `${jsonData.active_user_count}`;
countAllUser.innerText = `${jsonData.all_user_count}`;

tableRows = document.querySelector('.table-row');

function onLoad(){
    tableRowCode = ""
    jsonData.user_details.forEach(tableRow => {
        tableRowCode += `<tr>
                        <td class="people">
                            <img src="${tableRow['profile_picture']}" alt="">
                            <div class="people-de">
                                <h5>${tableRow['username']}</h5>
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
                        <td class="status">
                            <i class="fa fa-circle"></i>
                        </td>
                    </tr>`
    })
    tableRows.innerHTML = tableRowCode
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
        tableRowCode += `<tr>
                        <td class="people">
                            <img src="${tableRow['profile_picture']}" alt="">
                            <div class="people-de">
                                <h5>${tableRow['username']}</h5>
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
                        <td class="status">
                            <i class="fa fa-circle"></i>
                        </td>
                    </tr>`
    })
    tableRows.innerHTML = tableRowCode
    ActiveUsersDiv.classList.add('active');
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
        tableRowCode += `<tr>
                        <td class="people">
                            <img src="${tableRow['profile_picture']}" alt="">
                            <div class="people-de">
                                <h5>${tableRow['username']}</h5>
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
                        <td class="status">
                            <i class="fa fa-circle"></i>
                        </td>
                    </tr>`
    })
    tableRows.innerHTML = tableRowCode
    BlockedUsersDiv.classList.add('active');
    }
});