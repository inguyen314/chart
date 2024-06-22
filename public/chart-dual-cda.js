document.addEventListener('DOMContentLoaded', function () {
    // Display the loading indicator
    const loadingIndicator = document.getElementById('loading');
    loadingIndicator.style.display = 'block';

    console.log("============== calling chart-dual-cda.js ==============");

    // Define your datasets
    const datasets = [
        { cwms_ts_id: encodeURIComponent(cwms_ts_id)},
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_2)},
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_3)},
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_4)},
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_5)},
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_6)},
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_7)}
        // Add more datasets as needed
    ];
    console.log("datasets = ", datasets);

    // Filter out datasets where cwms_ts_id is null or undefined
    const validDatasets = datasets.filter(data => data.cwms_ts_id !== null && data.cwms_ts_id !== undefined && data.cwms_ts_id !== 'null');
    console.log("validDatasets = ", validDatasets);

    // Get current date and time
    const currentDateTime = new Date();
    // console.log('currentDateTime:', currentDateTime);

    // Subtract thirty hours from current date and time
    // const lookback = 96;
    const currentDateTimeMinus30Hours = subtractHoursFromDate(currentDateTime, lookback);
    // console.log('currentDateTimeMinus30Hours :', currentDateTimeMinus30Hours);

    // Map each dataset to its corresponding URL
    const urls = validDatasets.map(data => {
        const queryString = data.cwms_ts_id; // Assuming this is correct
        return `https://cwms-data.usace.army.mil/cwms-data/timeseries?name=${queryString}&begin=${currentDateTimeMinus30Hours.toISOString()}&end=${currentDateTime.toISOString()}&office=MVS`;
    });
    console.log("urls = ", urls);

    // Fetch all datasets simultaneously
    Promise.all(
        urls.map(url =>
            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json;version=2'
                }
            })
        )
    )
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

            const nonEmptyDatasets = datasets.filter(data => data.values && data.values.length > 0);
            console.log('nonEmptyDatasets:', nonEmptyDatasets);

            // First Location
            const firstDataset = nonEmptyDatasets[0];
            console.log('firstDataset:', firstDataset);
            const values = nonEmptyDatasets[0].values;
            const dateTimes = (values.map(item => item[0])).map(formatDate); // Adjusted to use formatDate function
            console.log('dateTimes:', dateTimes);

            const cwmsTsId = firstDataset.name;
            const unitId = firstDataset.units;
            const nameParts = firstDataset.name.split('.');
            const locationId = nameParts[0];
            const parameterId = nameParts[1];
            const intervalId = nameParts[3];
            const versionId = nameParts[5];
            console.log("locationId: ", locationId);  // St Louis-Mississippi
            console.log("parameterId: ", parameterId);  // Stage
            console.log("intervalId: ", intervalId);  // 15Minutes
            console.log("versionId: ", versionId);  // lrgsShef-rev

            // Second Location
            let parameterId2, unitId2 = null;
            if (nonEmptyDatasets.length > 1) {
                const secondDataset = nonEmptyDatasets[1];
                console.log('secondDataset:', secondDataset);
                const values2 = nonEmptyDatasets[1].values;
                const dateTimes2 = (values2.map(item => item[0])).map(formatDate); // Adjusted to use formatDate function
                console.log('dateTimes2:', dateTimes2);

                const cwmsTsId2 = secondDataset.name;
                unitId2 = secondDataset.units;
                const nameParts2 = secondDataset.name.split('.');
                const locationId2 = nameParts2[0];
                parameterId2 = nameParts2[1];
                console.log("locationId2: ", locationId2);  // St Louis-Mississippi
                console.log("parameterId2: ", parameterId2);  // Stage
                console.log("unitId2: ", unitId2);  // ft
            }

            // Chart Type Setup
            if (nonEmptyDatasets.length === 1 & (parameterId === "Stage" || parameterId === "Elev")) {
                console.log("===== For single line plot with location levels =====");

                const levelIdFlood = locationId + ".Stage.Inst.0.Flood";
                // console.log(levelIdFlood);
                const levelIdHingeMin = locationId + ".Height.Inst.0.Hinge Min";
                // console.log(levelIdHingeMin);
                const levelIdHingeMax = locationId + ".Height.Inst.0.Hinge Max";
                // console.log(levelIdHingeMax);
                const levelIdEffectiveDate = "2024-01-01T08:00:00";

                // Define the URLs to fetch related data from
                const url1 = `https://water.usace.army.mil/cwms-data/levels/${levelIdFlood}?office=MVS&effective-date=${levelIdEffectiveDate}&unit=ft`;
                const url2 = `https://water.usace.army.mil/cwms-data/levels/${levelIdHingeMin}?office=MVS&effective-date=${levelIdEffectiveDate}&unit=ft`;
                const url3 = `https://water.usace.army.mil/cwms-data/levels/${levelIdHingeMax}?office=MVS&effective-date=${levelIdEffectiveDate}&unit=ft`;
                console.log('url1:', url1);
                console.log('url2:', url2);
                console.log('url3:', url3);

                // Fetch the related data
                Promise.all([
                    fetch(url1).then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    }).catch(error => {
                        console.error('Error fetching data from url1:', error);
                        return null; // Return null if fetch failed
                    }),
                
                    fetch(url2).then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    }).catch(error => {
                        console.error('Error fetching data from url2:', error);
                        return null; // Return null if fetch failed
                    }),
                
                    fetch(url3).then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    }).catch(error => {
                        console.error('Error fetching data from url3:', error);
                        return null; // Return null if fetch failed
                    })
                ])
                .then(metaDataArray => {
                    console.log('metaDataArray:', metaDataArray);
                    
                    const floodLevelData = metaDataArray[0];
                    const hingeMinData = metaDataArray[1];
                    const hingeMaxData = metaDataArray[2];

                    const floodLevelTimeSeries = dateTimes.map(dateTime => {
                        // Check if floodLevelData contains a valid constant-level
                        if (floodLevelData && floodLevelData["constant-value"] !== undefined) {
                            return {
                                x: dateTime,
                                y: floodLevelData["constant-value"]
                            };
                        } else {
                            // Handle case where constant-level is not found or not valid
                            return {
                                x: dateTime,
                                y: null // or any default value you want to assign
                            };
                        }
                    });
                    console.log("floodLevelTimeSeries: ", floodLevelTimeSeries);

                    const hingeMinTimeSeries = dateTimes.map(dateTime => {
                        // Check if hingeMinData contains a valid constant-level
                        if (hingeMinData && hingeMinData["constant-value"] !== undefined) {
                            return {
                                x: dateTime,
                                y: hingeMinData["constant-value"]
                            };
                        } else {
                            // Handle case where constant-level is not found or not valid
                            return {
                                x: dateTime,
                                y: null // or any default value you want to assign
                            };
                        }
                    });
                    console.log("hingeMinTimeSeries: ", hingeMinTimeSeries);

                    const hingeMaxTimeSeries = dateTimes.map(dateTime => {
                        // Check if hingeMaxData contains a valid constant-level
                        if (hingeMaxData && hingeMaxData["constant-value"] !== undefined) {
                            return {
                                x: dateTime,
                                y: hingeMaxData["constant-value"]
                            };
                        } else {
                            // Handle case where constant-level is not found or not valid
                            return {
                                x: dateTime,
                                y: null // or any default value you want to assign
                            };
                        }
                    });
                    console.log("hingeMaxTimeSeries: ", hingeMaxTimeSeries);

                    const series = [
                        {
                            label: cwmsTsId,
                            parameter_id: parameterId,
                            unit_id: unitId,
                            data: firstDataset.values.map(item => ({ x: item[0], y: item[1] })), // Mapping values to x and y properties
                            borderColor: 'red',
                            backgroundColor: 'red',
                            tension: 0.5, // Adjust this value for the desired curve. 0: Represents straight lines. 1: Represents very smooth, rounded curves.
                            cubicInterpolationMode: 'default', // Set to 'default' for a solid and smooth line.
                            pointRadius: 0.5, // Set pointRadius to 0 to hide data point dots.
                            showLine: true, // Show the connecting line.
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
            } else if (nonEmptyDatasets.length === 1) {
                console.log("===== For Flow, Precip and Etc. =====");
                    const series = [
                        {
                            label: cwmsTsId, //'Dataset',
                            parameter_id: parameterId,
                            unit_id: unitId,
                            data: firstDataset.values.map(item => ({ x: item[0], y: item[1] })), // Mapping values to x and y properties
                            borderColor: 'red',
                            backgroundColor: 'red',
                            fill: false
                        }
                    ].filter(series => series);

                    // console.log(series);

                    plotData(series);

                    loadingIndicator.style.display = 'none';
               
            } else if (nonEmptyDatasets.length > 1 & (parameterId === parameterId2)) {
                console.log("===== For multiple line plots where parameter id are the same =====");
  
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
                    const cwmsTsId = data.name; // Retrieve cwmsTsId from each dataset
            
                    return {
                        label: cwmsTsId, // Unique label for each dataset
                        parameter_id: parameterId,
                        unit_id: unitId,
                        data: data.values.map(item => ({ x: item[0], y: item[1] })),
                        borderColor: colors[index % colors.length],
                        backgroundColor: colors[index % colors.length],
                        fill: false
                    };
                });

                // console.log(series);

                plotData(series);
                loadingIndicator.style.display = 'none';
            } else if (nonEmptyDatasets.length > 1 & (parameterId !== parameterId2)) {
                console.log("===== For multiple line plots where parameter id are NOT the same =====");
            
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
                    const cwmsTsId = data.name;
                    const parameterId = data.name.split('.')[1];
                    const unitId = data.units;
            
                    return {
                        label: cwmsTsId,
                        parameter_id: parameterId,
                        unit_id: unitId,
                        data: data.values.map(item => ({ x: item[0], y: item[1] })),
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

    // Extract unique parameter IDs for creating multiple y-axes, excluding null and undefined
    const uniqueParameterIds = [...new Set(datasets.map(item => item.parameter_id).filter(id => id != null))];
    console.log('uniqueParameterIds:', uniqueParameterIds);

   // Calculate initial minY and maxY from visible datasets
    let minY = null;
    let maxY = null;

    if (uniqueParameterIds.length === 1) {
        const initialMinMax = getInitialMinMaxY(datasets);
        minY = initialMinMax.minY;
        maxY = initialMinMax.maxY;
    } else {
        const initialMinMaxDual = getInitialMinMaxYDualAxis(datasets, uniqueParameterIds);
        minY = initialMinMaxDual.minY;
        maxY = initialMinMaxDual.maxY;
    }
    
    // Create y-axes configuration dynamically if there are unique parameter IDs
    let yScales = {};

    if (uniqueParameterIds.length > 1) {
        console.log("Multi Axis yScales");
        yScales = {
            y0: {
                min: minY.y0,
                max: maxY.y0,
                type: 'linear',
                position: 'left',
                title: {
                    display: true,
                    text: datasets[0].parameter_id + ' ' + '(' + datasets[0].unit_id + ')'
                }
            },
            y1: {
                min: minY.y1,
                max: maxY.y1,
                type: 'linear',
                position: 'right',
                title: {
                    display: true,
                    text: datasets[1].parameter_id + ' ' + '(' + datasets[1].unit_id + ')',
                }
            }
        };
    } else {
        console.log("Single Axis yScales");
        yScales = {
            y: {
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

            }
        };
    }
   

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
                ...yScales
            },
            plugins: {
                title: {
                    display: true,
                    text: "Cloud" + " " + datasets[0].parameter_id + " " + 'Plot Macro',
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

                        // Recalculate min and max Y values for visible datasets if there are multiple unique parameter IDs
                        if (uniqueParameterIds.length > 1) {
                            console.log("uniqueParameterIds.length > 1 @ onClick");
                            const visibleDatasets = chart.data.datasets.filter(dataset => !dataset.hidden);
                            const { minY, maxY } = getInitialMinMaxYDualAxis(visibleDatasets, uniqueParameterIds);

                            // Update y-axes min and max
                            Object.keys(chart.options.scales).forEach(scale => {
                                if (scale !== 'x' && minY[scale] !== undefined && maxY[scale] !== undefined) {
                                    chart.options.scales[scale].min = minY[scale];
                                    chart.options.scales[scale].max = maxY[scale];
                                }
                            });
                        } else {
                           // Recalculate min and max Y values for visible datasets
                            const visibleDatasets = chart.data.datasets.filter(dataset => !dataset.hidden);
                            const { minY, maxY } = getInitialMinMaxY(visibleDatasets);

                            // Update y-axes min and max
                            Object.keys(chart.options.scales).forEach(scale => {
                                if (scale !== 'x') {
                                    chart.options.scales[scale].min = minY;
                                    chart.options.scales[scale].max = maxY;
                                }
                            }); 
                        }

                        // Update the chart
                        chart.update();
                    }
                },
                beforeUpdate: function(chart) {
                    // Recalculate min and max Y values for visible datasets if there are multiple unique parameter IDs
                    if (uniqueParameterIds.length > 1) {
                        const visibleDatasets = chart.data.datasets.filter(dataset => !dataset.hidden);
                        const { minY, maxY } = getInitialMinMaxYDualAxis(visibleDatasets, uniqueParameterIds);

                        // Update y-axes min and max
                        Object.keys(chart.options.scales).forEach(scale => {
                            if (scale !== 'x' && minY[scale] !== undefined && maxY[scale] !== undefined) {
                                chart.options.scales[scale].min = minY[scale];
                                chart.options.scales[scale].max = maxY[scale];
                            }
                        });
                    } else {
                        // Filter visible datasets
                        const visibleDatasets = chart.data.datasets.filter(dataset => !dataset.hidden);
                        
                        // Recalculate min and max Y values for visible datasets
                        const { minY, maxY } = getInitialMinMaxY(visibleDatasets);

                        // Update y-axes min and max
                        Object.keys(chart.options.scales).forEach(scale => {
                            if (scale !== 'x') {
                                chart.options.scales[scale].min = minY;
                                chart.options.scales[scale].max = maxY;
                            }
                        });
                    }

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
    console.log("datasets @ getInitialMinMaxY ", datasets);

    let minY = Infinity; // Initialize minY to Infinity (highest possible number)
    let maxY = -Infinity; // Initialize maxY to -Infinity (lowest possible number)

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

    console.log('minDataY:', minDataY);
    console.log('maxDataY:', maxDataY);

    // Adjust minY and maxY based on the parameter_id
    if (datasets[0].parameter_id === "Stage") {
        if (minDataY <= 0) {
            minY = minDataY - 1;
            console.log("Stage: less than or equal to 0");
        } else if (minDataY <= 10) {
            minY = minDataY - 1;
            console.log("Stage: less than or equal to 10");
        } else if (minDataY > 200) {
            minY = minDataY - 1;
            console.log("Stage: greater than 200");
        } else {
            minY = minDataY - (minDataY * 0.1);
            console.log("Stage: minus 10%");
        }

        if (maxDataY <= 0) {
            maxY = maxDataY + 1;
            console.log("Stage: less than or equal to 0");
        } else if (maxDataY <= 10) {
            maxY = maxDataY + 1;
            console.log("Stage: less than or equal to 10");
        } else if (maxDataY > 200) {
            maxY = maxDataY + 1;
            console.log("Stage: greater than 200");
        } else {
            maxY = maxDataY + (maxDataY * 0.1);
            console.log("Stage: plus 10%");
        }

    } else if (datasets[0].parameter_id === "Flow") {
        if (minDataY <= 0) {
            minY = 0;
            console.log("Flow: less or equal to 0");
        } else if (minDataY <= 10) {
            minY = 0;
            console.log("Flow: greater than 0 but less or equal to 10");
        } else if (minDataY <= 50) {
            minY = Math.round(minDataY) - 2;
            console.log("Flow: greater than 10 but less or equal to 50");
        } else if (minDataY > 50000) {
            minY = (Math.round(minDataY / 1000) * 1000) - 5000;
            console.log("Flow: greater than 50,000");
        } else if (minDataY > 100000) {
            minY = (Math.round(minDataY / 1000) * 1000) - 10000;
            console.log("Flow: greater than 100,000");
        } else {
            minY = minDataY - (minDataY * 0.1);
            console.log("Flow: minus 10%");
        }

        if (maxDataY <= 10) {
            maxY = Math.round(maxDataY) + 5;
            console.log("Flow: less or equal to 10");
        } else if (maxDataY <= 50) {
            maxY = Math.round(maxDataY) + 5;
            console.log("Flow: greater than 10 but less or equal to 50");
        } else if (maxDataY > 50000) {
            maxY = (Math.round(maxDataY / 1000) * 1000) + 5000;
            console.log("Flow: greater than 50,000");
        } else if (maxDataY > 100000) {
            maxY = (Math.round(maxDataY / 1000) * 1000) + 10000;
            console.log("Flow: greater than 100,000");
        } else {
            maxY = maxDataY + (maxDataY * 0.1) + 5;
            console.log("Flow: plus 10%");
        }

    } else {
        // Default adjustments for other parameter_id values
        minY = minDataY - (minDataY * 0.1);
        maxY = maxDataY + (maxDataY * 0.1);
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

// Function to format date time to mm-dd-yyyy
function formatDate(timestamp) {
    const date = new Date(timestamp);
    const formattedDate = date.toLocaleString('en-US', {
        month: '2-digit',
        day: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: false
    });
    return formattedDate;
}

// Function to get minY and maxY for dual axis chart
function getInitialMinMaxYDualAxis(datasets, uniqueParameterIds) {
    console.log("getInitialMinMaxYDualAxis for dual axis");

    let minY = {
        y0: Infinity,
        y1: Infinity
    };
    let maxY = {
        y0: -Infinity,
        y1: -Infinity
    };

    // Arrays to store all yValues for each axis
    let allYValuesY0 = [];
    let allYValuesY1 = [];

    console.log("Initial minY:", minY);
    console.log("Initial maxY:", maxY);
    console.log("Initial allYValuesY0:", allYValuesY0);
    console.log("Initial allYValuesY1:", allYValuesY1);

    datasets.forEach((dataset, datasetIndex) => {
        console.log(`Processing dataset ${datasetIndex}:`, dataset);

        if (dataset.hidden) {
            console.log(`Dataset ${datasetIndex} is hidden, skipping.`);
            return; // Skip hidden datasets
        }

        const parameterIndex = uniqueParameterIds.indexOf(dataset.parameter_id);
        console.log(`parameterIndex for dataset ${datasetIndex}:`, parameterIndex);

        dataset.data.forEach((dataPoint, dataPointIndex) => {
            const yValue = parseFloat(dataPoint.y);
            console.log(`Data point ${dataPointIndex} (yValue: ${yValue}) in dataset ${datasetIndex}`);

            if (parameterIndex === 0) {
                allYValuesY0.push(yValue); // Collect yValues for y0 axis
                console.log(`Added ${yValue} to allYValuesY0`);
            } else if (parameterIndex === 1) {
                allYValuesY1.push(yValue); // Collect yValues for y1 axis
                console.log(`Added ${yValue} to allYValuesY1`);
            }
        });
    });

    console.log("Final allYValuesY0:", allYValuesY0);
    console.log("Final allYValuesY1:", allYValuesY1);

    // Find min and max yValues for y0 axis
    if (allYValuesY0.length > 0) {
        minY.y0 = Math.min(...allYValuesY0);
        maxY.y0 = Math.max(...allYValuesY0);
        console.log('Updated minY.y0:', minY.y0);
        console.log('Updated maxY.y0:', maxY.y0);
    }

    // Find min and max yValues for y1 axis
    if (allYValuesY1.length > 0) {
        minY.y1 = Math.min(...allYValuesY1);
        maxY.y1 = Math.max(...allYValuesY1);
        console.log('Updated minY.y1:', minY.y1);
        console.log('Updated maxY.y1:', maxY.y1);
    }

    // Adjust minY and maxY based on the axis-specific logic if needed

    console.log('Final minY:', minY);
    console.log('Final maxY:', maxY);

    // Return object with calculated minY and maxY for both y0 and y1 axes
    return { minY, maxY };
}

