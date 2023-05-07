console.log(jsonData.stat.stat_per_week_details[0])
const chart1 = document.querySelector('#chart-1');
const chart2 = document.querySelector('#chart-2');
const myChart = document.querySelector('#myChart');
const myChart2 = document.querySelector('#myChart2');

chart1.addEventListener('click', () => {
    myChart.style.display = 'block';
    myChart2.style.display = 'none';
    chart1.classList.add('active');
    chart2.classList.remove('active');
});

chart2.addEventListener('click', () => {
    myChart2.style.display = 'block';
    myChart.style.display = 'none';
    chart2.classList.add('active');
    chart1.classList.remove('active');
});



const data = {
    labels: ['Completed', 'High Priority', 'Low Priority', 'Medium Priority', 'Ongoing', 'Pending', 'Reviewed', 'To Do'],
    datasets: [
        {
            label: 'Task Count',
            data: [
                jsonData.stat.task_details.no_of_completed_tasks,
                jsonData.stat.task_details.no_of_high_priority_tasks,
                jsonData.stat.task_details.no_of_low_priority_tasks,
                jsonData.stat.task_details.no_of_medium_priority_tasks,
                jsonData.stat.task_details.no_of_ongoing_tasks,
                jsonData.stat.task_details.no_of_pending_tasks,
                jsonData.stat.task_details.no_of_reviewed_tasks,
                jsonData.stat.task_details.no_of_todo_tasks,
            ],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)',
                'rgba(201, 203, 207, 0.2)',
                'rgba(255, 99, 132, 0.4)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(201, 203, 207, 1)',
                'rgba(255, 99, 132, 1)',
            ],
            borderWidth: 1
        }
    ]
};

const options = {
    scales: {
        y: {
            beginAtZero: true,
            grid: {
                color: 'rgba(0, 0, 0, 0.2)'
            },
            ticks: {
                font: {
                    size: 12
                },
                color: 'rgba(0, 0, 0, 0.6)'
            },
            title: {
                display: true,
                text: 'Task Count',
                font: {
                    size: 14,
                    weight: 'bold'
                },
                color: 'rgba(0, 0, 0, 0.8)'
            }

        },
        x: {
            grid: {
                display: false
            },
            ticks: {
                font: {
                    size: 12
                },
                color: 'rgba(0, 0, 0, 0.6)'
            }
        }
    },

    animation: {
        duration: 4000
    },

    plugins: {
        title: {
            display: true,
            // text: 'Task Statistics',
            font: {
                size: 16,
                weight: 'bold'
            },
            color: 'rgba(0, 0, 0, 0.8)'
        },
        legend: {
            display: false
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleFont: {
                size: 14,
                weight: 'bold'
            },
            bodyFont: {
                size: 12
            },
            displayColors: false
        }
    }
};

const chart = new Chart('myChart', {
    type: 'bar',
    data: data,
    options: options
});


const myData = {
    labels: jsonData.stat.stat_per_week_details.map(item => `Week ${item.week}`),
    datasets: [
        {
            label: 'Number of Completed Tasks',
            data: jsonData.stat.stat_per_week_details.map(item => item.no_of_completed_tasks),
            fill: 'start', // Use a gradient fill effect
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 2,
            pointBackgroundColor: 'white', // Change the color of the data points
            pointBorderColor: 'rgba(255, 99, 132, 1)',
            pointBorderWidth: 1,
            pointRadius: 4, // Change the size of the data points
            pointStyle: 'rectRounded' // Change the shape of the data points
        }
    ]
};

const myOptions = {
    scales: {
        x: {
            ticks: {
                color: 'rgba(0, 0, 0, 0.5)' // Change the color of the x-axis ticks
            }
        },
        y: {
            beginAtZero: true,
            grid: {
                color: 'rgba(0, 0, 0, 0.2)'
            },
            ticks: {
                font: {
                    size: 12
                },
                color: 'rgba(0, 0, 0, 0.6)'
            },
            title: {
                display: true,
                text: 'Completed Tasks Count',
                font: {
                    size: 14,
                    weight: 'bold'
                },
                color: 'rgba(0, 0, 0, 0.8)'
            }

        }
    },
    plugins: {
        legend: {
            display: false,
            labels: {
                color: 'rgba(0, 0, 0, 0.8)', // Change the color of the legend labels
                font: {
                    size: 14 // Change the font size of the legend labels
                }
            }
        },
        title: {
            display: false,
            text: 'Number of Completed Tasks', // Add a title to the graph
            color: 'rgba(0, 0, 0, 0.8)',
            font: {
                size: 18 // Change the font size of the title
            },
            padding: {
                top: 10 // Add some padding to the top of the title
            }
        }
    }
};

var myLineGraph = new Chart(
    document.getElementById('myChart2'),
    {
        type: 'line',
        data: myData,
        options: myOptions
    }
);
