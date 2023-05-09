const notificationPopupBtn = document.querySelector('.notification-bell-btn')
const notificationPopup = document.querySelector('.notification-popup-container');
const notificationPopupCloseBtn = document.querySelector('.notification-popup-close-btn');
const notificationPopupContainer = document.querySelector('.notification-popup-container');
const container = document.querySelector('.container');

notificationPopupBtn.addEventListener('click', () => notificationPopup.classList.add('active'));
notificationPopupCloseBtn.addEventListener('click', () => notificationPopup.classList.remove('active'));
notificationPopup.addEventListener('click', () => notificationPopup.classList.remove('active'))

$notifications = [];

notificationPopup.addEventListener('click', () => {
    notificationPopup.classList.remove('active');
});


function adjustNotificationPopupMargin() {
    const notificationPopup = document.querySelector('.notification-popup');
    const screenWidth = window.innerWidth;
    const baseWidth = 1290;
    const baseMarginLeft = 950;
    const marginReductionFactor = 1;
    const maxMarginLeft = 1175;
  
    // Calculate the new margin
    let newMarginLeft = baseMarginLeft - (baseWidth - screenWidth) * marginReductionFactor;
    newMarginLeft = Math.min(newMarginLeft, maxMarginLeft);
    newMarginLeft = newMarginLeft - 20;
    // Set the new margin
    notificationPopup.style.marginLeft = `${newMarginLeft}px`;
    // console.log(notificationPopup.style.marginLeft);
  }
  
  // Call the function initially
  adjustNotificationPopupMargin();
  
  // Attach the function to the window resize event
  window.addEventListener('resize', adjustNotificationPopupMargin);
  