async function createCommitCalendar() {

  // Define the contribution levels and colors
  var levels = [
    { value: 0, color: '#ebedf0', text: 'No contributions' },
    { value: 1, color: '#c6e48b', text: '1 contribution' },
    { value: 2, color: '#c6e48b', text: '1 contribution' },
    { value: 3, color: '#7bc96f', text: '3 contributions' },
    { value: 4, color: '#7bc96f', text: '3 contributions' },
    { value: 5, color: '#7bc96f', text: '3 contributions' },
    { value: 6, color: '#239a3b', text: '6 contributions' },
    { value: 7, color: '#239a3b', text: '6 contributions' },
    { value: 8, color: '#239a3b', text: '6 contributions' },
    { value: 9, color: '#196127', text: '9 contributions' }
  ];

  var level_legend = [
    { value: 0, color: '#ebedf0', text: 'No contributions' },
    { value: 1, color: '#c6e48b', text: '1 < 3 contribution' },
    { value: 3, color: '#7bc96f', text: '3 < 6 contributions' },
    { value: 6, color: '#239a3b', text: '6 < 9 contributions' },
    { value: 9, color: '#196127', text: '9 < contributions' }
  ];

  var tasks = jsonData.user_info.commits;
  if(tasks == 0){
    tasks = [ { date: '0', count: 0 }]
  }
  // extract date information and group tasks by date
  const tasksByDate = tasks.reduce((acc, task) => {
    const date = task.date;
    acc[date] = acc[date] || [];
    acc[date].push(task);
    return acc;
  }, {});

  // count tasks by date and create array of objects with date and count
  const result = Object.entries(tasksByDate).map(([date, tasks]) => {
    return { date, count: tasks.length };
  });
  var myData = result.map(function (item) {
    var count = item.count;
    if (count > 0 && count < 3) {
      count = 1;
    } else if (count >= 3 && count < 6) {
      count = 3;
    } else if (count >= 6 && count < 9) {
      count = 6;
    } else if (count >= 9) {
      count = 9;
    }
    return { date: item.date, count: count };
  });

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

    // Add the commit squares for the month using your own data
    for (var j = 1; j <= 31; j++) {
      var cell = document.createElement('td');
      cell.style.textAlign = 'center';

      if (j > month.daysInMonth()) {
        cell.innerHTML = '';
      } else {
        // Find the data for the current date
        var data = myData.find(function (item) {
          return item.date === year + '-' + month.format('MM') + '-' + j.toString().padStart(2, '0');
        });
        var count = data ? data.count : 0;
        var active = month.isSame(moment(), 'monthCalender') && j === moment().date();
        var commitSquare = document.createElement('div');
        commitSquare.classList.add('commit');
        commitSquare.style.backgroundColor = levels[count].color;
        if (active) {
          commitSquare.classList.add('active');
        }
        commitSquare.dataset.date = `${year}-${month.format('MM')}-${j.toString().padStart(2, '0')}`;

        commitSquare.addEventListener('mouseenter', function (event) {
          var tooltip = document.createElement('div');
          tooltip.innerText = event.target.dataset.date;
          tooltip.style.position = 'absolute';
          tooltip.style.top = (event.pageY + 10) + 'px';
          tooltip.style.left = (event.pageX + 10) + 'px';
          tooltip.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
          tooltip.style.color = '#fff';
          tooltip.style.padding = '5px';
          tooltip.style.borderRadius = '5px';
          tooltip.style.fontSize = '10px';
          tooltip.style.zIndex = 999;
          document.body.appendChild(tooltip);
          commitSquare.tooltip = tooltip;
        });

        commitSquare.addEventListener('mouseleave', function (event) {
          if (commitSquare.tooltip) {
            commitSquare.tooltip.parentNode.removeChild(commitSquare.tooltip);
            commitSquare.tooltip = null;
          }
        });

        cell.appendChild(commitSquare);
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
  for (var k = 0; k < level_legend.length; k++) {
    var level = level_legend[k];
    var legendItem = document.createElement('div');
    legendItem.style.flex = 1;
    legendItem.style.textAlign = 'center';
    legendItem.innerHTML = '<span class="legend-item" style="display: inline-block; width: 8px; height: 8px; margin-right: 0.5rem; background-color: ' + level.color + ';"></span>' + '<span class="text" style="display: inline-block; font-size = "10px";>' + level.text + '</span>';
    legend.appendChild(legendItem);
  }

  var commit_container = document.getElementById('commit_container');
  commit_container.appendChild(table);
  commit_container.appendChild(legend);

}

createCommitCalendar();