const ProfilePictureImg = document.getElementById('profile-picture-img');
const fullScreenBtn = document.getElementById("full-screen-btn");
const normalScreenBtn = document.getElementById("normal-screen-btn");
const container1 = document.querySelector(".container-1");
const container2 = document.querySelector(".container-2");
const videoContainer2 = document.querySelector(".video-container-2");

const userData = jsonData.user_data;
ProfilePictureImg.setAttribute("src", userData.profile_picture);


fullScreenBtn.addEventListener("click", function() {
  container1.style.width = "50%";
  container2.style.width = "50%";
  videoContainer2.style.height = "95%";
  PeerProfileImg.style.marginTop = "20px";
});
normalScreenBtn.addEventListener("click", function() {
    container1.style.width = "70%";
    container2.style.width = "30%";
    videoContainer2.style.height = "40%";
    PeerProfileImg.style.marginTop = "-20px";
  });

// Get the microphone icons
const micIcon = document.getElementsByClassName('mic-on')[0];
const micSlashIcon = document.getElementsByClassName('mic-off')[0];

// Add event listener to the microphone icon
micIcon.addEventListener('click', () => {
  // Hide the microphone icon and show the microphone slash icon
  micIcon.classList.remove('active');
  micSlashIcon.classList.remove('active');
});

// Add event listener to the microphone slash icon
micSlashIcon.addEventListener('click', () => {
  // Hide the microphone slash icon and show the microphone icon
  micSlashIcon.classList.add('active');
  micIcon.classList.add('active');
});

const camIcon = document.getElementsByClassName('cam-on')[0];
const camSlashIcon = document.getElementsByClassName('cam-off')[0];

// Add event listener to the microphone icon
camIcon.addEventListener('click', () => {
  // Hide the microphone icon and show the microphone slash icon
  camIcon.classList.remove('active');
  camSlashIcon.classList.remove('active');
});

// Add event listener to the microphone slash icon
camSlashIcon.addEventListener('click', () => {
  // Hide the microphone slash icon and show the microphone icon
  camSlashIcon.classList.add('active');
  camIcon.classList.add('active');
});