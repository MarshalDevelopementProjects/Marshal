const leftPanel = document.querySelector('.left-panel');
const middle = document.querySelector('.middle-icon');
const middle2 = document.querySelector('.middle-icon2');
const sideBtn = document.querySelector('.fa-caret-left');
const topMenu = document.querySelector('.top-menus');
const menu = document.querySelector('.menu');
const menuIcon = document.querySelector('.menu-icon');
const calender = document.querySelector('.bottom-calender');
const calenderBtn = document.querySelector('.calender');
const rightPanel = document.querySelector('.right-panel');


middle.addEventListener('click', function(){
    leftPanel.classList.add('active');
    delay(addCalenderFunction, 200);
    delay(addFunction, 300);
    
});
middle2.addEventListener('click', function(){
    delay(removeFunction, 700);
    delay(removeCalenderFunction, 900);
    leftPanel.classList.remove('active');
    
});

calenderBtn.addEventListener('click', function(){
    delay(removeFunction, 600);
    delay(removeCalenderFunction, 800);
    leftPanel.classList.remove('active');
    
});

function delay(callback, time) {
    setTimeout(callback, time);
  }
  
  function addFunction() {
    leftPanel.classList.add('active');
    topMenu.classList.add('active');
    sideBtn.classList.add('active');
    middle.classList.add('active');
    middle2.classList.add('active');
    menu.classList.add('active');
    menuIcon.classList.add('active');
    rightPanel.classList.add('active');
  }
  function removeFunction(){
    rightPanel.classList.remove('active');
    topMenu.classList.remove('active');
    sideBtn.classList.remove('active');
    middle.classList.remove('active');
    middle2.classList.remove('active');
    menu.classList.remove('active');
    menuIcon.classList.remove('active');
  }
  
  function addCalenderFunction() {
    calender.classList.add('active');
  }
  function removeCalenderFunction(){
    calender.classList.remove('active');
  }
 
  


const dashboard = document.querySelector('.dashboard');
const profile = document.querySelector('.profile');
const settings = document.querySelector('.settings');
const sketchIdea = document.querySelector('.sketch-idea');

dashboard.addEventListener('click', function(){
    dashboard.classList.add('active');
    profile.classList.remove('active');
    newProject.classList.remove('active');
    sketchIdea.classList.remove('active');

})

profile.addEventListener('click', function(){
    dashboard.classList.remove('active');
    profile.classList.add('active');
    newProject.classList.remove('active');
    sketchIdea.classList.remove('active');


})

sketchIdea.addEventListener('click', function(){
    dashboard.classList.remove('active');
    profile.classList.remove('active');
    newProject.classList.remove('active');
    sketchIdea.classList.add('active');

    window.location.href = "./index.html";
})




