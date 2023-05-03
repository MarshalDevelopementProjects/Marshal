const UserProfilePictureImg = document.querySelector(".profile-image");
if (jsonData.user_data && jsonData.user_data.profile_picture) {
    UserProfilePictureImg.setAttribute("src", jsonData.user_data.profile_picture);
}

/* <a href="http://localhost/public/user/profile"><img id="profile-picture-img" alt="/App" class="profile-image"></a> */