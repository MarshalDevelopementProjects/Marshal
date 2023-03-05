const LogOutButton = document.getElementById("log-out-btn");
const EditProfilePictureForm = document.getElementById("edit-profile-picture-form");
const EditProfileFrom = document.getElementById("edit-profile-form");
const EditProfileBtn = document.getElementById("edit-profile-btn");
const SaveChangesBtn = document.getElementById("save-changes-btn");
const CancelChangesBtn = document.getElementById("cancel-changes-btn");
const UsernameHeader = document.getElementById("username-header");
const BioHeader = document.getElementById("bio-header");
const Username = document.getElementById("user-name");
const FirstName = document.getElementById("first_name");
const LastName = document.getElementById("last_name");
const EmailAddress = document.getElementById("email_address");
const Bio = document.getElementById("bio");
const PhoneNumber = document.getElementById("phone_number");
const Position = document.getElementById("position");
const Status = document.getElementById("status");
const titleElement = document.getElementById("title");
const VerifyPasswordFromDiv = document.getElementById("verify-password-form-div");
const changePwdTitle = document.getElementById("change-pwd-title");
const UpdatePasswordFormDiv = document.getElementById("update-password-form-div");
const ProfilePictureImg = document.getElementById("profile-picture-img");
const ProfilePicture = document.getElementById("profile-img");
const ChangePasswordBtn = document.getElementById("change-password-btn");
const submitButton = document.getElementById("submitButton");
const profileImg = document.getElementById("profile-img");
const popupWrapper = document.querySelector(".wrapper-container");

// ==============================================================
// user profile information


// this function is used to load the user information to the relevant fields on load
// or on page refresh after editing the profile information
function onLoad() {
    console.log("sdfh");
    console.log(jsonData.user_info.username);
    UsernameHeader.innerText = jsonData.user_info.username;
    // BioHeader.value = jsonData.user_info.bio;
    Username.value = jsonData.user_info.username;
    FirstName.value = jsonData.user_info.first_name;
    LastName.value = jsonData.user_info.last_name;
    Bio.value = jsonData.user_info.bio;
    EmailAddress.value = jsonData.user_info.email_address;
    Position.value = jsonData.user_info.position;
    PhoneNumber.value = jsonData.user_info.phone_number;
    // Status.value = jsonData.user_info.user_status;
    ProfilePictureImg.src = jsonData.user_info.display_picture;
    ProfilePicture.src = jsonData.user_info.display_picture;
    console.log(ProfilePictureImg.src);
    Username.disabled = true;
    FirstName.disabled = true;
    LastName.disabled = true;
    Bio.disabled = true;
    EmailAddress.disabled = true;
    Position.disabled = true;
    PhoneNumber.disabled = true;
    // Status.disabled = true;
    // SaveChangesBtn.disabled = true;
    // CancelChangesBtn.disabled = true;
    SaveChangesBtn.setAttribute("style", "display: none");
    CancelChangesBtn.setAttribute("style", "display: none");
    ChangePasswordBtn.setAttribute("style", "display: none");
    popupWrapper.style.display = "none";
};
onLoad();
// ==============================================================
// edit form buttons
const pwdDiv = document.getElementById("pwdDiv");
EditProfileBtn.addEventListener('click', function (event) {
    event.preventDefault();
    Username.disabled = false;
    FirstName.disabled = false;
    LastName.disabled = false;
    Bio.disabled = false;
    EmailAddress.disabled = false;
    Position.disabled = false;
    PhoneNumber.disabled = false;
    // Status.disabled = false;
    SaveChangesBtn.disabled = false;
    CancelChangesBtn.disabled = false;
    SaveChangesBtn.setAttribute("style", "display: inline");
    CancelChangesBtn.setAttribute("style", "display: inline");
    ChangePasswordBtn.setAttribute("style", "display: inline");
    pwdDiv.setAttribute("style", "display: block");
    titleElement.textContent = "Edit Profile";
    EditProfileBtn.classList.add("hide");
    if (pwdDiv.classList.contains("hide")) {
        pwdDiv.classList.remove("hide");
        changePwdTitle.setAttribute("style", "display: none");
        VerifyPasswordFromDiv.setAttribute("style", "display: none; animation: fadeIn 1s ease;");
        UpdatePasswordFormDiv.setAttribute("style", "display: none; transition: opacity 1s");
    }

    // EditProfileBtn.setAttribute("style", "display: none");
});
// ==============================================================
// cancel changes btn
CancelChangesBtn.addEventListener('click', function (event) {
    event.preventDefault();
    EditProfileBtn.classList.remove("hide");
    pwdDiv.classList.add("hide")
    onLoad();
    // pwdDiv.classList.remove("hide")
});
// ==============================================================

// LogOutButton.addEventListener("click", () => {
//     fetch("http://localhost/public/user/logout", {
//             withCredentials: true,
//             credentials: "include",
//             mode: "cors",
//             method: "POST",
//         })
//         .then((response) => {
//             if (response.ok) {
//                 window.location.replace("http://localhost/public/user/login");
//                 return;
//             }
//             if (!response.ok) {
//                 response.json();
//             }
//         })
//         .then((data) => {
//             if (data.message != undefined && data.message != undefined) {
//                 alert(data.message);
//             } else {
//                 alert(data.message);
//             }
//         })
//         .catch((error) => {
//             console.error(error)
//         });
// });

fileInput = document.querySelector(".file-input"),

    EditProfilePictureForm.addEventListener("click", () => {
        fileInput.click();
    });

submitButton.addEventListener('click', async function (event) {
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
        location.reload();
    }
});

profileImg.addEventListener("click", function() {
    popupWrapper.style.display = "block";
  });

SaveChangesBtn.addEventListener('click', async function (event) {
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

ChangePasswordBtn.addEventListener('click', async function (event) {
    event.preventDefault();
    // on click make a popup and prompt the user whether he wants to proceed or not
    VerifyPasswordFromDiv.setAttribute("style", "display: inline; animation: fadeIn 1s ease;");
    changePwdTitle.setAttribute("style", "display: block");
    ChangePasswordBtn.setAttribute("style", "display: none");

    const VerifyPasswordFrom = document.getElementById("verify-password-form");
    VerifyPasswordFrom.addEventListener("submit", async function (event) {
        event.preventDefault();
        try {
            let formData_1 = new FormData(VerifyPasswordFrom);
            let formDataObj_1 = Object.fromEntries(formData_1);
            let response_1 = await fetch(
                "http://localhost/public/user/edit/password", {
                withCredentials: true,
                credentials: "include",
                mode: "cors",
                method: "POST",
                body: formData_1
            }
            );

            let data_1 = await response_1.json();
            if (response_1.ok) {
                // another popup to ask the user whether he wants to proceed or not
                alert(data_1.message);
                UpdatePasswordFormDiv.setAttribute("style", "display: inline; transition: opacity 1s");
                UpdatePasswordFormDiv.style.opacity = "1";
                VerifyPasswordFromDiv.setAttribute("style", "display: none; animation: fadeIn 1s ease;");
                const UpdatePasswordForm = document.getElementById("update-password-form");
                UpdatePasswordForm.addEventListener('submit', async function (event) {
                    event.preventDefault();
                    let formData_2 = new FormData(UpdatePasswordForm);
                    let formDataObj_2 = Object.fromEntries(formData_2);
                    let response_2 = await fetch(
                        "http://localhost/public/user/edit/password", {
                        withCredentials: true,
                        credentials: "include",
                        mode: "cors",
                        method: "PUT",
                        body: JSON.stringify(formDataObj_2)
                    }
                    );

                    let data_2 = await response_2.json();
                    alert(data_2.message);
                    location.reload();
                });
            } else {
                alert(data_1.message);
                location.reload();
            }
        } catch (error) {
            console.error(error);
        }
    });
});