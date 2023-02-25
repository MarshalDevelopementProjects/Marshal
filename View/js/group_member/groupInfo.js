

const notificationPopupBtn = document.querySelector('.notification-bell-btn')
const notificationPopup = document.querySelector('.notification-popup-container');
const notificationPopupCloseBtn = document.querySelector('.notification-popup-close-btn');
const notificationPopupContainer = document.querySelector('.notification-popup-container');
const container = document.querySelector('.container');

notificationPopupBtn.addEventListener('click', () => notificationPopup.classList.add('active'));
notificationPopupCloseBtn.addEventListener('click', () => notificationPopup.classList.remove('active'));
notificationPopup.addEventListener('click', () => notificationPopup.classList.remove('active'))





const createGroupPopup = document.querySelector('.create-group-container'),
createGroupBtn = document.querySelector('#create-group-btn'),
createGroupCancelBtn = document.querySelector('#cancel-create-group');

createGroupBtn.addEventListener('click', () => {
  createGroupPopup.classList.add('active');
})
createGroupCancelBtn.addEventListener('click', () => {
  createGroupPopup.classList.remove('active')
})