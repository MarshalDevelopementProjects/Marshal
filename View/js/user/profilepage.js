const LogOutButton = document.getElementById("log-out-btn");

const EditProfilePictureForm = document.getElementById("edit-profile-picture-form");
const EditProfileFrom = document.getElementById("edit-profile-form");
const EditProfileBtn = document.getElementById("edit-profile-btn");
const SaveChangesBtn = document.getElementById("save-changes-btn");
const CancelChangesBtn = document.getElementById("cancel-changes-btn");

const UsernameHeader = document.getElementById("username-header");
const BioHeader = document.getElementById("bio-header");

const Username = document.getElementById("username");
const FirstName = document.getElementById("first_name");
const LastName = document.getElementById("last_name");
const EmailAddress = document.getElementById("email_address");
const Bio = document.getElementById("bio");
const PhoneNumber = document.getElementById("phone_number");
const Position = document.getElementById("position");
const Status = document.getElementById("status");

const ProfilePictureImg = document.getElementById("profile-picture-img");

// ==============================================================
// user profile information
console.log(jsonData);

// this function is used to load the user information to the relevant fields on load
// or on page refresh after editing the profile information
const OnLoad = async function() {
    UsernameHeader.innerText = jsonData.user_info.username;
    BioHeader.innerText = jsonData.user_info.bio;
    Username.value = jsonData.user_info.username;
    FirstName.value = jsonData.user_info.first_name;
    LastName.value = jsonData.user_info.last_name;
    Bio.value = jsonData.user_info.bio;
    EmailAddress.value = jsonData.user_info.email_address;
    Position.value = jsonData.user_info.position;
    PhoneNumber.value = jsonData.user_info.phone_number;
    Status.value = jsonData.user_info.user_status;
    ProfilePictureImg.src = jsonData.user_info.display_picture;
    Username.disabled = true;
    FirstName.disabled = true;
    LastName.disabled = true;
    Bio.disabled = true;
    EmailAddress.disabled = true;
    Position.disabled = true;
    PhoneNumber.disabled = true;
    Status.disabled = true;
    SaveChangesBtn.disabled = true;
    CancelChangesBtn.disabled = true;
    SaveChangesBtn.setAttribute("style", "display: none");
    CancelChangesBtn.setAttribute("style", "display: none");
};
// ==============================================================
// edit form buttons
EditProfileBtn.addEventListener('click', function(event) {
    event.preventDefault();
    Username.disabled = false;
    FirstName.disabled = false;
    LastName.disabled = false;
    Bio.disabled = false;
    EmailAddress.disabled = false;
    Position.disabled = false;
    PhoneNumber.disabled = false;
    Status.disabled = false;
    SaveChangesBtn.disabled = false;
    CancelChangesBtn.disabled = false;
    SaveChangesBtn.setAttribute("style", "display: inline");
    CancelChangesBtn.setAttribute("style", "display: inline");
});
// ==============================================================
// cancel changes btn
CancelChangesBtn.addEventListener('click', function(event) {
    event.preventDefault();
    OnLoad();
});
// ==============================================================

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

EditProfilePictureForm.addEventListener('submit', async function(event) {
    event.preventDefault();
    const inputFile = document.getElementById("profile-picture");
    let formData = new FormData();
    let file = inputFile.files[0];
    formData.append("profile_picture", file);
    try {
        let response = await fetch("http://localhost/public/user/profile/edit/picture", {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "POST",
            body: formData,
            headers: {
                "IMAGE_TYPE": file.type,
                "IMAGE_NAME": file.name
            }
        });
        // make this a pop up
        let message = (await response.json()).message;
        if (response.ok) {
            alert(message);
            location.reload();
        } else {
            alert(message);
        }

    } catch (error) {
        console.error(error);
    }
});

EditProfileFrom.addEventListener('submit', async function(event) {
    event.preventDefault();
    let formData = new FormData(EditProfileFrom);
    let jsonFormData = JSON.stringify(Object.fromEntries(formData));
    console.log(jsonFormData);
    try {
        let response = await fetch("http://localhost/public/user/profile/edit", {
            withCredentials: true,
            credentials: "include",
            mode: "cors",
            method: "PUT",
            body: jsonFormData
        });

        let returnData = await response.json();
        console.log(returnData);
        if (response.ok) {
            jsonData.user_info = returnData.user_info;
            OnLoad();
        }
        alert(returnData.message);
    } catch (error) {
        // alert(error.message);
        console.error(error);
    }
});