document.addEventListener('DOMContentLoaded', function () {
    // Display the loading_alarm_mvs indicator
    const loadingIndicator = document.getElementById('loading');
    loadingIndicator.style.display = 'block';

    const dataToSend = {
        cwms_ts_id: encodeURIComponent(cwms_ts_id),
        start_day: encodeURIComponent(start_day),
        end_day: encodeURIComponent(end_day),
    };
    const queryString = Object.keys(dataToSend).map(key => key + '=' + dataToSend[key]).join('&');
    const url = `https://wm.mvs.ds.usace.army.mil/php-data-api/public/get_ts_lookback.php?${queryString}`;
    console.log("url = ", url);

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Check if data_items array is present in the data
            console.log('data: ', data);

            // console.log(data[0].cwms_ts_id);
            // console.log(data[0].date_time);
            // console.log(data[0].location_id);
            // console.log(data[0].parameter_id);
            // console.log(data[0].quality_code);
            // console.log(data[0].unit_id);
            // console.log(data[0].value);
            // console.log(data[0].version_id);

            const minMaxAxisValues = calculateAxisValues(data);
            console.log("minMaxAxisValues:", minMaxAxisValues.minValueY1);
            console.log("minMaxAxisValues:", minMaxAxisValues.maxValueY1);
            
            // Call the function to create chart
            createChart(data, minMaxAxisValues, start_day)

            // Call the function to creat data table
            printHtmlTable(data);

            // Hide the gage_data indicator
            loadingIndicator.style.display = 'none';
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            // Hide the loading_alarm_mvs indicator regardless of success or failure
            loadingIndicator.style.display = 'none';
        });
});

// Function to find min and max
function findMinMax(jsonArray) {
    const values = jsonArray
        .map(item => parseFloat(item.value))
        .filter(value => !isNaN(value) && value !== null);
        
    const min = Math.min(...values);
    const max = Math.max(...values);
    
    return { min, max };
}

// Function to check Daylight Saving Time
function isDaylightSavingTime(date) {
    const january = new Date(date.getFullYear(), 0, 1);
    const july = new Date(date.getFullYear(), 6, 1);
    const stdTimezoneOffset = Math.max(january.getTimezoneOffset(), july.getTimezoneOffset());
    return date.getTimezoneOffset() < stdTimezoneOffset;
}

// Function to find axis min and max with padding
function calculateAxisValues(data) {
    const result = findMinMax(data);

    let minValueY1, maxValueY1;
    const firstParameterId = data[0].parameter_id;

    if (firstParameterId === "Stage" || firstParameterId === "Elev") {
        console.log("Add Padding to Stage Min Max Values");

        // Calculate min value for Stage or Elev
        if (result.min <= 0) { 
            minValueY1 = result.min - 1;
            console.log("less than 0");
        } else if (result.min <= 10) {
            minValueY1 = result.min - 1;
            console.log("less than 10");
        } else if (result.min > 200) {
            console.log("greater than 200");
            minValueY1 = result.min - 1;
        } else {
            minValueY1 = result.min - (result.min * 0.1);
            console.log("minus 10%");
        }
        console.log("minValueY1 = ", minValueY1);

        // Calculate max value for Stage or Elev
        if (result.max <= 0) {
            console.log("less than 0");
            maxValueY1 = result.max + 1;
        } else if (result.max <= 10) {
            console.log("less than 10");
            maxValueY1 = result.max + 1;
        } else if (result.max > 200) {
            console.log("greater than 200");
            maxValueY1 = result.max + 1;
        } else {
            maxValueY1 = result.max + (result.max * 0.1);
            console.log("plus 10%");
        }
        console.log("maxValueY1 = ", maxValueY1);

    } else if (firstParameterId === "Flow") {
        console.log("Add Padding to Flow Min Max Values");

        // Calculate min value for Flow
        if (result.min <= 0) {
            minValueY1 = 0;
            console.log("flow is less than or equal to 0");
        } else if (result.min > 0 && result.min <= 10) {
            minValueY1 = 0;
            console.log("flow is greater than zero but less than or equal to 10");
        } else if (result.min > 10 && result.min <= 50) {
            minValueY1 = Math.round(result.min) - 2;
            console.log("greater than 0 and less than or equal to 50");
        } else if (result.min > 50000) {
            minValueY1 = (Math.round(result.min / 1000) * 1000) - 5000;
            console.log("greater than 50,000");
        } else if (result.min > 100000) {
            minValueY1 = (Math.round(result.min / 1000) * 1000) - 10000;
            console.log("greater than 100,000");
        } else {
            minValueY1 = result.min - (result.min * 0.1);
            console.log("minus 10%");
        }
        console.log("minValueY1 = ", minValueY1);

        // Calculate max value for Flow
        if (result.max > 0 && result.max <= 10) {
            maxValueY1 = Math.round(result.max) + 5;
            console.log("greater than or equal to 0 and less than or equal to 10");
        } else if (result.max > 10 && result.max <= 50) {
            maxValueY1 = Math.round(result.max) + 5;
            console.log("greater than 10 and less than or equal to 50");
        } else if (result.max > 50000) {
            maxValueY1 = (Math.round(result.max / 1000) * 1000) + 5000;
            console.log("greater than 50,000");
        } else if (result.max > 100000) {
            maxValueY1 = (Math.round(result.max / 1000) * 1000) + 10000;
            console.log("greater than 100,000");
        } else {
            maxValueY1 = result.max + (result.max * 0.1) + 5;
            console.log("plus 10%");
        }
        console.log("maxValueY1 = ", maxValueY1);

    } else {
        console.log("Add Padding to Misc Min Max Values");

        // Calculate min value for everything else
        minValueY1 = result.min - (result.min * 0.1);
        console.log("minValueY1 = ", minValueY1);

        // Calculate max value for everything else
        maxValueY1 = result.max + (result.max * 0.1) + 1;
        console.log("maxValueY1 = ", maxValueY1);
    }

    return { minValueY1, maxValueY1 };
}

// Function to generate and print an HTML table into a specified div
function printHtmlTable(data) {
    // Create a table element
    var table = document.createElement('table');
    table.setAttribute('id', 'gage_data'); // Set the id to "customers"

    // Create a table header row
    var headerRow = table.insertRow();
    var headers = ['Date Time', 'Value'];

    // Loop through headers and create table headers
    headers.forEach(function(headerText) {
        var header = document.createElement('th');
        var textNode = document.createTextNode(headerText);
        header.appendChild(textNode);
        headerRow.appendChild(header);
    });

    // Iterate over each data entry and create table rows
    data.forEach(function(entry) {
        var row = table.insertRow();
        
        // Parse the date_time string into a Moment.js object
        var momentObj = moment(entry.date_time);
        // Format the date_time using Moment.js
        var formattedDateTime = momentObj.format("YYYY-MM-DD HH:mm:ss");

        // Create cell for date_time
        var dateTimeCell = row.insertCell();
        var dateTimeText = document.createTextNode(formattedDateTime);
        dateTimeCell.appendChild(dateTimeText);

        // Create cell for value and format it to fixed 2 decimal places
        var valueCell = row.insertCell();
        var formattedValue = parseFloat(entry.value).toFixed(2);
        var valueText = document.createTextNode(formattedValue);
        valueCell.appendChild(valueText);
    });

    // Get the div element by its ID
    var tableDiv = document.getElementById('table');

    // Clear existing content in the div
    tableDiv.innerHTML = '';

    // Append the table to the div
    tableDiv.appendChild(table);
}

function createChart(data, minMaxAxisValues, start_day) {
    // Prepare arrays for Chart.js
    var labels = data.map(entry => moment(entry.date_time)); // Use Moment.js for parsing
    var values = data.map(entry => parseFloat(entry.value)); // Array of numeric values

    // Get the canvas element
    var ctx = document.getElementById('myChart').getContext('2d');

    // Create the chart
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: data[0].cwms_ts_id,
                data: values,
                fill: false,
                borderColor: 'rgba(255, 0, 0, 1)', // red
                backgroundColor: 'rgba(255, 0, 0, 1)', // red
                tension: 0.5,
                showLine: true, // Show the connecting line
                pointRadius: 1, // Set pointRadius to 0 to hide data point dots
            }]
        },
        options: {
            scales: {
                x: {
                    type: 'time', // Assuming X-axis is a time scale
                    time: {
                        unit: 'hour',
                        stepSize: 6,
                        tooltipFormat: 'MMM D, YYYY HH:mm',
                        displayFormats: {
                            hour: 'HH:mm',
                            minute: 'HH:mm',
                        }
                    },
                    grid: {
                        display: true,
                        lineWidth: (context) => {
                            const index = context.index;
                            const ticks = context.chart.scales.x.ticks;
                            const tickIndex = context.index;

                            if (ticks && tickIndex >= 0 && tickIndex < ticks.length) {
                                const tick = ticks[tickIndex];
                                if (tick) {
                                    const tickLabel = tick.label;
                                    if (tickLabel == "06:00" || tickLabel == "12:00" || tickLabel == "18:00") {
                                        return 1;
                                    } else {
                                        return 3;
                                    }
                                }
                            }
                        },
                        color: (context) => {
                            return 'rgba(150, 150, 150, 0.8)';
                        },
                    },
                    ticks: {
                        callback: function (value) {
                            const date = new Date(value);
                            const current_hour_utc = date.getUTCHours() === 0 ? 24 : date.getUTCHours();
                            const DaylightSavingTime = isDaylightSavingTime(date) ? 5 : 6;
                            const current_hour_cst = current_hour_utc - DaylightSavingTime;

                            if (current_hour_cst === 6 || current_hour_cst === 12 || current_hour_cst === 18) {
                                return new Intl.DateTimeFormat('en-US', {
                                    hour: 'numeric',
                                    minute: 'numeric',
                                    hour12: false,
                                    timeZone: 'America/Chicago'
                                }).format(new Date(value));
                            } else if (current_hour_cst === 0) {
                                return new Intl.DateTimeFormat('en-US', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric',
                                    hour12: false,
                                    timeZone: 'America/Chicago',
                                }).format(new Date(value));
                            }
                        },
                        maxRotation: 90,
                        minRotation: 90,
                    },
                    title: {
                        display: true,
                        text: 'Date Time',
                        fontSize: 16,
                        fontColor: 'black',
                    }
                },
                y: {
                    suggestedMin: minMaxAxisValues.minValueY1,
                    suggestedMax: minMaxAxisValues.maxValueY1,
                    type: 'linear',
                    position: 'left',
                    title: {
                        display: true,
                        text: data[0].parameter_id + ' ' + '(' + data[0].unit_id + ')',
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var value = context.parsed.y;
                            var datasetLabel = context.dataset.label;
                            if (data[0].parameter_id == "Stage" || data[0].parameter_id == "Conc-DO" || data[0].parameter_id == "Elev") {
                                return value.toFixed(2) + " " + data[0].unit_id + " " + datasetLabel + " (Y1-axis)";
                            } else if (data[0].parameter_id == "Flow") {
                                if (value.toFixed(2) < 1000) {
                                    return value.toFixed(0) + " " + data[0].unit_id + " " + datasetLabel + " (Y1-axis)";
                                } else {
                                    return (Math.floor(value / 10) * 10) + " " + data[0].unit_id + " " + datasetLabel + " (Y1-axis)";
                                }
                            } else {
                                return value.toFixed(2) + " " + datasetLabel + " (Y1-axis)";
                            }
                        }
                    }
                },
                title: {
                    display: true,
                    text: start_day + ' Day ' + data[0].parameter_id,
                    position: 'top',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                }
            },
            legend: {
                display: true,
                position: 'bottom',
            },
            layout: {
                padding: {
                    top: 10,
                    right: 20,
                    bottom: 10,
                    left: 20
                }
            }
        }
    });
}