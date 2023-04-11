// Define the contribution levels and colors
var levels = [  { value: 0, color: '#ebedf0', text: 'No contributions' },  { value: 1, color: '#c6e48b', text: '1 contribution' },  { value: 3, color: '#7bc96f', text: '3 contributions' },  { value: 6, color: '#239a3b', text: '6 contributions' },  { value: 9, color: '#196127', text: '9 contributions' }];

// Get the current year
var year = moment().year();

// Create a table for the calendar
var table = document.createElement('table');
table.style.width = '100%';

// Add the year to the table
var thead = document.createElement('thead');
var row = document.createElement('tr');
var yearCell = document.createElement('th');
yearCell.colSpan = 31;
yearCell.innerHTML = year;
yearCell.classList.add('yearCalender');
row.appendChild(yearCell);
thead.appendChild(row);
table.appendChild(thead);

// Add a row for each month
for (var i = 0; i < 12; i++) {
  var month = moment().month(i);

  // Create a row for the month
  row = document.createElement('tr');
  var monthCell = document.createElement('td');
  monthCell.innerHTML = month.format('MMM');
  monthCell.classList.add('monthCalender');
  row.appendChild(monthCell);

  // Add the commit squares for the month
  for (var j = 1; j <= 31; j++) {
    var cell = document.createElement('td');
    cell.style.textAlign = 'center';

    if (j > month.daysInMonth()) {
      cell.innerHTML = '';
    } else {
      var count = Math.floor(Math.random() * levels.length);
      var active = month.isSame(moment(), 'monthCalender') && j === moment().date();
      cell.innerHTML = '<div class="commit' + (active ? ' active' : '') + '" style="background-color: ' + levels[count].color + ';"></div>';
    }

    row.appendChild(cell);
  }

  table.appendChild(row);
}

// Add the contribution level legend
var legend = document.createElement('div');
legend.classList.add('legend');
legend.style.display = 'flex';
legend.style.marginTop = '1rem';
for (var k = 0; k < levels.length; k++) {
  var level = levels[k];
  var legendItem = document.createElement('div');
  legendItem.style.flex = 1;
  legendItem.style.textAlign = 'center';
  legendItem.innerHTML = '<span class="legend-item" style="display: inline-block; width: 8px; height: 8px; margin-right: 0.5rem; background-color: ' + level.color + ';"></span>' + '<span class="text" style="display: inline-block; font-size = "10px";>' + level.text + '</span>' ;
  legend.appendChild(legendItem);
}

var commit_container = document.getElementById('commit_container');
commit_container.appendChild(table);
commit_container.appendChild(legend);


