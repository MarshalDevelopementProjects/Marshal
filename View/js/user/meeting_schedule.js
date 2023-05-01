const upcome = document.getElementsByClassName('upmeeting-title-container')[0];
const old = document.getElementsByClassName('oldmeeting-title-container')[0];
const scheduleBoxOld = document.getElementsByClassName('schedule-box-old')[0];
const scheduleBoxNew = document.getElementsByClassName('schedule-box-new')[0];

console.log(scheduleBoxOld);
console.log(scheduleBoxNew);

// Add event listener to the microphone icon
upcome.addEventListener('click', () => {
  // Hide the microphone icon and show the microphone slash icon
  upcome.classList.remove('active');
  old.classList.remove('active');
  scheduleBoxOld.classList.remove('active');
  scheduleBoxNew.classList.remove('active');
});

// Add event listener to the microphone slash icon
old.addEventListener('click', () => {
  // Hide the microphone slash icon and show the microphone icon
  upcome.classList.add('active');
  old.classList.add('active');
  scheduleBoxNew.classList.add('active');
  scheduleBoxOld.classList.add('active');
});
