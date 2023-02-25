// calendor 

function lastMondayOfMonth(month, year) {
    // Create a new date object set to the last day of the given month and year
    var date = new Date(year, month, 0);
  
    // Set the date to the last Monday before the last day of the month
    while (date.getDay() !== 1) {
      date.setDate(date.getDate() - 1);
    }
  
    // Get the date (day of the month) of the last Monday
    var lastMondayDate = date.getDate();
  
    return lastMondayDate;
  }

const monthText = document.querySelector('.month'),
yearText = document.querySelector('.year'),
daysTxt = document.querySelector('.days');

var months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

var currentDate = new Date(),
year = currentDate.getFullYear(),
month = currentDate.getMonth();

var monthState = 0;

const previousMonthBtn = document.querySelector('.previous-month-btn');
const nextMonthBtn = document.querySelector('.next-month-btn');

nextMonthBtn.addEventListener('click', function(){
    monthState += 1;

    monthText.innerHTML = months[(currentDate.getMonth() + monthState) % 12];
    
    if((currentDate.getMonth() + monthState) % 12 == 0){
        year += 1;
    }
    if(monthState == 12){
        monthState = 0;
    }
    yearText.innerHTML = year;
    month = currentDate.getMonth() + monthState;

    daysTxt.innerHTML = renderDays(year, month, monthState);
    console.log(monthState);

})

previousMonthBtn.addEventListener('click', function(){

    if(currentDate.getMonth() + monthState == 0){
        year -= 1;
    }
    if(monthState == 0){
        monthState = 12;
    }
    monthState -= 1;
    console.log(monthState);

    monthText.innerHTML = months[(currentDate.getMonth() + monthState) % 12];

    yearText.innerHTML = year;
    month = currentDate.getMonth() + monthState;

    daysTxt.innerHTML = renderDays(year, month, monthState);
})
const renderDays = (year, month, monthState) => {

    var checkWeek = 0;
    var dayStatus = 'inactive';
    var lastMonthStart = lastMondayOfMonth(month, year);

    var lastMonthEnd = new Date(year, currentDate.getMonth() + monthState, 0).getDate();
    var currentMonthEnd = new Date(year, currentDate.getMonth() + 1 + monthState, 0).getDate();
    var dayNo = lastMonthStart;
    // console.log(lastMonthStart)

    var code = "";
    for(var i=0; i<42; i++){

        if(checkWeek == 0){
            code += '<div class="days-line">'
        }
        if(dayNo > lastMonthEnd && dayStatus == 'inactive'){
            dayStatus = 'active';
            dayNo = 1;
        }
        if(dayNo > currentMonthEnd && dayStatus == 'active'){
            dayStatus = 'inactive';
            dayNo = 1;
        }

        if(dayStatus == 'active' && monthState == 0 && i == currentDate.getDate() + (lastMonthEnd - lastMonthStart)){
            code += `<p class="day today ${dayStatus}">${dayNo}</p>`;
        }else if(monthState == 0 && i %11 == 1 && dayStatus == 'active'){
            code += `<p class="day deadline ${dayStatus}">${dayNo}</p>`;
        }
        else{
            code += `<p class="day ${dayStatus}">${dayNo}</p>`;
        }
        
        dayNo += 1;
        checkWeek += 1;

        if(checkWeek == 7){
            code += '</div>';
            checkWeek = 0;
        }
    }
    return code;
}


const renderCalendar = (year, month) => {
    // getting last date of month
    // let lastDateOfMonth = new Date(year, currentDate.getMonth() + 1, 0).getDate();

    monthText.innerHTML = months[month];
    yearText.innerHTML = year;

    daysTxt.innerHTML = renderDays(year, month, monthState);
};
renderCalendar(year, month);