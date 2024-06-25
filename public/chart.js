document.addEventListener('DOMContentLoaded', function () {
    // Display the loading indicator
    const loadingIndicator = document.getElementById('loading');
    loadingIndicator.style.display = 'block';

    // Define your datasets
    const datasets = [
        { cwms_ts_id: encodeURIComponent(cwms_ts_id), start_day: encodeURIComponent(start_day), end_day: encodeURIComponent(end_day) },
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_2), start_day: encodeURIComponent(start_day), end_day: encodeURIComponent(end_day) },
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_3), start_day: encodeURIComponent(start_day), end_day: encodeURIComponent(end_day) },
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_4), start_day: encodeURIComponent(start_day), end_day: encodeURIComponent(end_day) },
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_5), start_day: encodeURIComponent(start_day), end_day: encodeURIComponent(end_day) },
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_6), start_day: encodeURIComponent(start_day), end_day: encodeURIComponent(end_day) },
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_7), start_day: encodeURIComponent(start_day), end_day: encodeURIComponent(end_day) }
        // Add more datasets as needed
    ];
    console.log("datasets = ", datasets);

    // Get current date and time
    const currentDateTime = new Date();
    console.log('currentDateTime:', currentDateTime);

    // Subtract two hours from current date and time
    const currentDateTimeMinus2Hours = subtractHoursFromDate(currentDateTime, 2);
    console.log('currentDateTimeMinus2Hours :', currentDateTimeMinus2Hours);

    // Subtract thirty hours from current date and time
    const currentDateTimeMinus30Hours = subtractHoursFromDate(currentDateTime, 64);
    console.log('currentDateTimeMinus30Hours :', currentDateTimeMinus30Hours);

    // Add thirty hours to current date and time
    const currentDateTimePlus30Hours = plusHoursFromDate(currentDateTime, 30);
    console.log('currentDateTimePlus30Hours :', currentDateTimePlus30Hours);

    // Add four days to current date and time
    const currentDateTimePlus4Days = addDaysToDate(currentDateTime, 4);
    console.log('currentDateTimePlus4Days :', currentDateTimePlus4Days);

    // Map each dataset to its corresponding URL
    const urls = datasets.map(data => {
        const queryString = Object.keys(data).map(key => key + '=' + data[key]).join('&');
        return `https://wm.mvs.ds.usace.army.mil/php_data_api/public/get_ts_lookback.php?${queryString}`;
    });
    console.log("urls = ", urls);

    // Fetch all datasets simultaneously
    Promise.all(urls.map(url => fetch(url)))
        .then(responses => {
            return Promise.all(responses.map(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            }));
        })
        .then(datasets => {
            console.log('datasets:', datasets);

            const nonEmptyDatasets = datasets.filter(data => data && data.length > 0);

            const firstDataset = nonEmptyDatasets[0];
            const parameterId = firstDataset[0].parameter_id;
            const parameterId2 = firstDataset[1].parameter_id;

            console.log('parameterId:', parameterId);
            console.log('parameterId2:', parameterId2);

            // For single line plot
            if (nonEmptyDatasets.length === 1 & (parameterId === "Stage" || parameterId === "Elev")) {
                // Only one dataset has data, plot both dataset and flood levels
                const firstDataset = nonEmptyDatasets[0];
                const locationId = firstDataset[0].location_id;
                const cwmsTsId = firstDataset[0].cwms_ts_id;
                const parameterId = firstDataset[0].parameter_id;
                const unitId = firstDataset[0].unit_id;
                const dateTimes = firstDataset.map(item => item.date_time);

                // Define the URLs to fetch related data from
                const url1 = `https://wm.mvs.ds.usace.army.mil/php_data_api/public/get_specified_level_id_level.php?location_id=${locationId}&specified_level_id_level=Flood`;
                const url2 = `https://wm.mvs.ds.usace.army.mil/php_data_api/public/get_specified_level_id_level.php?location_id=${locationId}&specified_level_id_level=Hinge Min`;
                const url3 = `https://wm.mvs.ds.usace.army.mil/php_data_api/public/get_specified_level_id_level.php?location_id=${locationId}&specified_level_id_level=Hinge Max`;

                // Fetch the related data
                Promise.all([
                    fetch(url1).then(response => response.json()),
                    fetch(url2).then(response => response.json()),
                    fetch(url3).then(response => response.json())
                ])
                .then(dataArray => {
                    console.log('dataArray:', dataArray);
                    
                    const floodLevelData = dataArray[0];
                    const hingeMinData = dataArray[1];
                    const hingeMaxData = dataArray[2];

                    const floodLevelTimeSeries = dateTimes.map(dateTime => ({
                        x: dateTime,
                        y: floodLevelData["constant_level"]
                    }));

                    const hingeMinTimeSeries = hingeMinData !== null ? dateTimes.map(dateTime => ({
                        x: dateTime,
                        y: hingeMinData["constant_level"]
                    })) : [];

                    const hingeMaxTimeSeries = hingeMaxData !== null ? dateTimes.map(dateTime => ({
                        x: dateTime,
                        y: hingeMaxData["constant_level"]
                    })) : [];

                    const series = [
                        {
                            label: cwmsTsId, //'Dataset',
                            parameter_id: parameterId,
                            unit_id: unitId,
                            data: firstDataset.map(item => ({ x: item.date_time, y: item.value })),
                            borderColor: 'red',
                            backgroundColor: 'red',
                            fill: false
                        },
                        {
                            label: 'Flood Level',
                            data: floodLevelTimeSeries,
                            borderColor: 'blue',
                            backgroundColor: 'blue',
                            fill: false,
                            hidden: true // Initially hidden
                        },
                        hingeMinData !== null && {
                            label: 'Hinge Min',
                            data: hingeMinTimeSeries,
                            borderColor: 'black',
                            backgroundColor: 'black',
                            fill: false,
                            hidden: true // Initially hidden
                        },
                        hingeMaxData !== null && {
                            label: 'Hinge Max',
                            data: hingeMaxTimeSeries,
                            borderColor: 'black',
                            backgroundColor: 'black',
                            fill: false,
                            hidden: true // Initially hidden
                        }
                    ].filter(series => series);

                    // console.log(series);

                    plotData(series);

                    loadingIndicator.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error fetching related data:', error);
                    loadingIndicator.style.display = 'none';
                });
            // For Flow, Precip and Etc.
            } else if (nonEmptyDatasets.length === 1) {
                // Only one dataset has data, plot both dataset and flood levels
                const firstDataset = nonEmptyDatasets[0];
                const locationId = firstDataset[0].location_id;
                const cwmsTsId = firstDataset[0].cwms_ts_id;
                const parameterId = firstDataset[0].parameter_id;
                const unitId = firstDataset[0].unit_id;
                const dateTimes = firstDataset.map(item => item.date_time);

                // Define the URLs to fetch related data from
                const url1 = `https://wm.mvs.ds.usace.army.mil/php_data_api/public/get_specified_level_id_level.php?location_id=${locationId}&specified_level_id_level=Flood`;
                const url2 = `https://wm.mvs.ds.usace.army.mil/php_data_api/public/get_specified_level_id_level.php?location_id=${locationId}&specified_level_id_level=Hinge Min`;
                const url3 = `https://wm.mvs.ds.usace.army.mil/php_data_api/public/get_specified_level_id_level.php?location_id=${locationId}&specified_level_id_level=Hinge Max`;

                // Fetch the related data
                Promise.all([
                    fetch(url1).then(response => response.json()),
                    fetch(url2).then(response => response.json()),
                    fetch(url3).then(response => response.json())
                ])
                .then(dataArray => {
                    console.log('dataArray:', dataArray);
                    
                    const series = [
                        {
                            label: cwmsTsId, //'Dataset',
                            parameter_id: parameterId,
                            unit_id: unitId,
                            data: firstDataset.map(item => ({ x: item.date_time, y: item.value })),
                            borderColor: 'red',
                            backgroundColor: 'red',
                            fill: false
                        } 
                    ].filter(series => series);

                    // console.log(series);

                    plotData(series);

                    loadingIndicator.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error fetching related data:', error);
                    loadingIndicator.style.display = 'none';
                });
            // For multiple line plots where parameter id are the same
            } else if (nonEmptyDatasets.length > 1 & (parameterId === parameterId2)) {
                // More than one dataset has data, plot both dataset only
                const firstDataset = nonEmptyDatasets[0];
                const locationId = firstDataset[0].location_id;
                const cwmsTsId = firstDataset[0].cwms_ts_id;
                const parameterId = firstDataset[0].parameter_id;
                const unitId = firstDataset[0].unit_id;
                const dateTimes = firstDataset.map(item => item.date_time);

                // More than one dataset has data, plot only the datasets
                const colors = [
                    'rgba(255, 99, 132, 1)',   // Red
                    'rgba(54, 162, 235, 1)',   // Blue
                    'rgba(75, 192, 192, 1)',   // Teal
                    'rgba(153, 102, 255, 1)',  // Purple
                    'rgba(255, 159, 64, 1)',   // Orange
                    'rgba(255, 206, 86, 1)',   // Yellow
                    'rgba(231, 233, 237, 1)'   // Gray
                ];

                const series = nonEmptyDatasets.map((data, index) => {
                    const cwmsTsId = data[0].cwms_ts_id; // Retrieve cwmsTsId from each dataset
            
                    return {
                        label: cwmsTsId, // Unique label for each dataset
                        parameter_id: data[0].parameter_id,
                        unit_id: data[0].unit_id,
                        data: data.map(item => ({ x: item.date_time, y: item.value })),
                        borderColor: colors[index % colors.length],
                        backgroundColor: colors[index % colors.length],
                        fill: false
                    };
                });

                // console.log(series);

                plotData(series);
                loadingIndicator.style.display = 'none';
            // For multiple line plots where parameter ids are NOT the same
            } else if (nonEmptyDatasets.length > 1 & (parameterId !== parameterId2)) {
                // More than one dataset has data, plot both dataset only
                const firstDataset = nonEmptyDatasets[0];
                const locationId = firstDataset[0].location_id;
                const cwmsTsId = firstDataset[0].cwms_ts_id;
                const parameterId = firstDataset[0].parameter_id;
                const unitId = firstDataset[0].unit_id;
                const dateTimes = firstDataset.map(item => item.date_time);

                // More than one dataset has data, plot only the datasets
                const colors = [
                    'rgba(255, 99, 132, 1)',   // Red
                    'rgba(54, 162, 235, 1)',   // Blue
                    'rgba(75, 192, 192, 1)',   // Teal
                    'rgba(153, 102, 255, 1)',  // Purple
                    'rgba(255, 159, 64, 1)',   // Orange
                    'rgba(255, 206, 86, 1)',   // Yellow
                    'rgba(231, 233, 237, 1)'   // Gray
                ];

                const series = nonEmptyDatasets.map((data, index) => {
                    const cwmsTsId = data[0].cwms_ts_id;
                    const parameterId = data[0].parameter_id;
            
                    return {
                        label: cwmsTsId,
                        parameter_id: parameterId,
                        unit_id: data[0].unit_id,
                        data: data.map(item => ({ x: item.date_time, y: item.value })),
                        borderColor: colors[index % colors.length],
                        backgroundColor: colors[index % colors.length],
                        fill: false,
                        yAxisID: parameterId // Assign the y-axis ID based on parameterId
                    };
                });

                // console.log(series);

                plotData(series);
                loadingIndicator.style.display = 'none';
            } else {
                console.log('No valid datasets to display.');
                loadingIndicator.style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching data:', error);
            loadingIndicator.style.display = 'none';
        });
});

// Function to plot data using Chart.js
function plotData(datasets) {
    console.log('datasets:', datasets);

    // Calculate initial minY and maxY from visible datasets
    const { minY, maxY } = getInitialMinMaxY(datasets);

    const ctx = document.getElementById('myChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'line',
        data: {
            datasets: datasets
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
                        fontColor: 'black',
                        font: {
                            size: 14 // Set the font size for the y-axis title
                        }
                    }
                },
                y: {
                    // Set initial min and max based on initial calculation
                    min: minY,
                    max: maxY,
                    type: 'linear',
                    position: 'left',
                    title: {
                        display: true,
                        text: datasets[0].parameter_id + ' ' + '(' + datasets[0].unit_id + ')',
                        font: {
                            size: 14 // Set the font size for the y-axis title
                        }
                    },
                    ticks: {
                        font: {
                            size: 12 // Set the font size for the y-axis labels
                        }
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: datasets[0].parameter_id + " " + 'Plot Macro',
                    font: {
                        size: 24 // Set the font size for the title
                    }
                },
                legend: {
                    display: true,
                    onClick: function(e, legendItem, legend) {
                        const index = legendItem.datasetIndex;
                        const meta = chart.getDatasetMeta(index);
                        const dataset = chart.data.datasets[index];

                        // Toggle visibility of the clicked dataset
                        dataset.hidden = !dataset.hidden;

                        // Recalculate min and max Y values for visible datasets
                        const { minY, maxY } = getInitialMinMaxY(chart.data.datasets);

                        console.log('Updated minY:', minY);
                        console.log('Updated maxY:', maxY);

                        // Update y-axis min and max
                        chart.options.scales.y.min = minY;
                        chart.options.scales.y.max = maxY;

                        // Update the chart
                        chart.update();
                    }
                },
                beforeUpdate: function(chart) {
                    const visibleDatasets = chart.data.datasets.filter(dataset => !dataset.hidden);
                    const { minY, maxY } = getInitialMinMaxY(visibleDatasets);

                    chart.options.scales.y.min = minY;
                    chart.options.scales.y.max = maxY;

                    console.log('Updated minY:', minY);
                    console.log('Updated maxY:', maxY);
                }
            }
        }
    });
}

// Function to check Daylight Saving Time
function isDaylightSavingTime(date) {
    const january = new Date(date.getFullYear(), 0, 1);
    const july = new Date(date.getFullYear(), 6, 1);
    const stdTimezoneOffset = Math.max(january.getTimezoneOffset(), july.getTimezoneOffset());
    return date.getTimezoneOffset() < stdTimezoneOffset;
}

// Function to calculate min and max Y values from visible datasets
function getMinMaxY(datasets) {
    let minY = Infinity;
    let maxY = -Infinity;

    datasets.forEach((dataset) => {
        if (dataset.hidden) return; // Skip hidden datasets
        dataset.data.forEach((dataPoint) => {
            const yValue = parseFloat(dataPoint.y);
            if (yValue < minY) {
                minY = yValue;
            }
            if (yValue > maxY) {
                maxY = yValue;
            }
        });
    });

    return { minY, maxY };
}

// Function to calculate initial min and max Y values from visible datasets
function getInitialMinMaxY(datasets) {
    let minY = Infinity; // Initialize minY to Infinity (highest possible number)
    let maxY = -Infinity; // Initialize maxY to -Infinity (lowest possible number)

    console.log("datasets getInitialMinMaxY ", datasets);

    // Log initial minY and maxY before adjustments
    console.log('Before adjustments:');
    console.log('Initial minY:', minY);
    console.log('Initial maxY:', maxY);

    // Arrays to store all yValues for calculating min and max
    let allYValues = [];

    datasets.forEach((dataset) => {
        if (dataset.hidden) return; // Skip hidden datasets

        dataset.data.forEach((dataPoint) => {
            const yValue = parseFloat(dataPoint.y);
            allYValues.push(yValue); // Collect all yValues from visible datasets
        });
    });

    // Find min and max yValues from collected array
    const minDataY = Math.min(...allYValues);
    const maxDataY = Math.max(...allYValues);

    console.log('minDataY:', typeof(minDataY));
    console.log('maxDataY:', maxDataY);

    // Set minY and maxY based on calculated minDataY and maxDataY
    if (datasets[0].parameter_id === "Stage") {
        if (minDataY < 0) {
            minY = minDataY - 1; // Adjust minY for Stage parameter
            maxY = maxDataY + 1; // Adjust maxY for Stage parameter
        } else {
            minY = minDataY - (minDataY*0.1); // Adjust minY for Stage parameter
            maxY = maxDataY + (maxDataY*0.1); // Adjust maxY for Stage parameter
        }
    } else if (datasets[0].parameter_id === "Flow") { // Adjustments for Flow parameter
        // minY = minDataY - 10; // Example adjustment for Flow
        if (minDataY <= 0) {
            minY = 0;
            console.log("flow id less and equal to 0");
        } else if (minDataY > 0 && minDataY <= 10) {
            minY = 0;
            console.log("flow is greater than zero but less or equal to 10");
        } else if (minDataY > 10 && minDataY <= 50) {
            minY = Math.round(minDataY) - 2;
            console.log("greater than 0 and less than 50");
        } else if (minDataY > 50000) {
            minY = (Math.round(minDataY/1000)*1000) - 5000;
            console.log("greater than 50,000");
        } else if (minDataY > 100000){
            minY = (Math.round(minDataY/1000)*1000) - 10000;
            console.log("greater than 100,000");
        } else {
            minY = minDataY - ((minDataY*0.1));
            console.log("minus 10%");
        }
        console.log("minY = ", minY);

        // maxY = maxDataY + 10; // Example adjustment for Flow
        if (maxDataY > 0 && maxDataY <= 10) {
            maxY = Math.round(maxDataY) + 5;
            console.log("greater and equal to 0 and less than 50");
        } else if (maxDataY > 10 && maxDataY <= 50) {
            maxY = Math.round(maxDataY) + 5;
            console.log("greater and equal to 0 and less than 50");
        } else if (maxDataY > 50000) {
            maxY = (Math.round(maxDataY/1000)*1000) + 5000;
            console.log("greater than 50,000");
        } else if (maxDataY > 100000) {
            maxY = (Math.round(maxDataY/1000)*1000) + 10000;
            console.log("greater than 100,000");
        } else {
            maxY = maxDataY + ((maxDataY*0.1)+5);
            console.log("plus 10%");
        }
        console.log("maxY = ", maxY);
    } else {
        // Default adjustments for other parameter_id values
        minY = minDataY - (minDataY*0.1); // Set minY to minDataY if no specific adjustment needed
        maxY = maxDataY + (maxDataY*0.1); // Set maxY to maxDataY if no specific adjustment needed
    }

    // Log adjusted minY and maxY after adjustments
    console.log('After adjustments:');
    console.log('Calculated minY:', minY);
    console.log('Calculated maxY:', maxY);

    // Return object with calculated minY and maxY
    return { minY, maxY };
}

// Function to get current data time
function subtractHoursFromDate(date, hoursToSubtract) {
    return new Date(date.getTime() - (hoursToSubtract * 60 * 60 * 1000));
}

// Function to get current data time
function plusHoursFromDate(date, hoursToSubtract) {
    return new Date(date.getTime() + (hoursToSubtract * 60 * 60 * 1000));
}

// Function to add days to a given date
function addDaysToDate(date, days) {
    return new Date(date.getTime() + (days * 24 * 60 * 60 * 1000));
}