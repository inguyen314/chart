// Function to plot data using Chart.js
function plotData(datasets) {
    // console.log('datasets @ plotData: ', datasets);

    // Extract unique parameter IDs for creating multiple y-axes, excluding null and undefined
    const uniqueParameterIds = [...new Set(datasets.map(item => item.parameter_id).filter(id => id != null))];
    // console.log('uniqueParameterIds:', uniqueParameterIds);

    // Create a mapping for unique parameter IDs to 'y0' and 'y1'
    const parameterIdToYAxis = {};

    if (uniqueParameterIds.length === 1) {
        // If there's only one parameterId, map it to both 'y0' and 'y1'
        const parameterId = uniqueParameterIds[0];
        parameterIdToYAxis[parameterId] = 'y';
        // parameterIdToYAxis[parameterId] = 'y1'; // or choose 'y1', depending on your logic
    } else {
        // If there are two parameterIds, map them alternately to 'y0' and 'y1'
        uniqueParameterIds.forEach((id, index) => {
            parameterIdToYAxis[id] = index % 2 === 0 ? 'y0' : 'y1';
        });
    }

    // Log the entire mapping object
    // console.log('parameterIdToYAxis:', parameterIdToYAxis);

    // Apply the mapping to the datasets
    datasets.forEach(dataset => {
        dataset.yAxisID = parameterIdToYAxis[dataset.parameter_id];
    });

    // Calculate initial minY and maxY from visible datasets
    let minY, maxY;

    if (uniqueParameterIds.length === 1) {
        const initialMinMax = getInitialMinMaxY(datasets); // Implement getInitialMinMaxY function if not already defined
        minY = initialMinMax.minY;
        maxY = initialMinMax.maxY;
    } else {
        const initialMinMaxDual = getInitialMinMaxYDualAxis2(datasets, uniqueParameterIds); // Implement getInitialMinMaxYDualAxis2 function if not already defined
        minY = initialMinMaxDual.minY;
        maxY = initialMinMaxDual.maxY;
    }

    // console.log('minY:', minY);
    // console.log('maxY:', maxY);

    // Create y-axes configuration dynamically if there are unique parameter IDs
    let yScales = {};

    if (uniqueParameterIds.length > 1) {
        // console.log("Dual Axis yScales");
        yScales = {
            y0: {
                min: minY.y0,
                max: maxY.y0,
                type: 'linear',
                position: 'left',
                title: {
                    display: true,
                    text: datasets.find(ds => parameterIdToYAxis[ds.parameter_id] === 'y0').parameter_id + ' (' + datasets.find(ds => parameterIdToYAxis[ds.parameter_id] === 'y0').unit_id + ')'
                }
            },
            y1: {
                min: minY.y1,
                max: maxY.y1,
                type: 'linear',
                position: 'right',
                title: {
                    display: true,
                    text: datasets.find(ds => parameterIdToYAxis[ds.parameter_id] === 'y1').parameter_id + ' (' + datasets.find(ds => parameterIdToYAxis[ds.parameter_id] === 'y1').unit_id + ')'
                }
            }
        };
    } else {
        // console.log("Single Axis yScales");
        yScales = {
            y: {
                min: minY,
                max: maxY,
                type: 'linear',
                position: 'left',
                title: {
                    display: true,
                    text: datasets[0].parameter_id + ' (' + datasets[0].unit_id + ')',
                    font: {
                        size: 14 // Set the font size for the y-axis title
                    }
                }
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
                        text: 'Date Time' + " " + "(" + datasets[0].time_zone + ")",
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
                    text: 'Cloud' + ' ' + 'Chart Macro', //'Cloud' + ' ' + datasets[0].parameter_id + ' ' + 'Plot Macro',
                    font: {
                        size: 24 // Set the font size for the title
                    }
                },
                legend: {
                    display: true,
                    onClick: function(e, legendItem, legend) {
                        // Determine if there are multiple unique parameter IDs
                        if (uniqueParameterIds.length > 1) {
                            // console.log("onClick Dual Chart");
                    
                            const index = legendItem.datasetIndex;
                            const meta = chart.getDatasetMeta(index);
                            const dataset = chart.data.datasets[index];
                    
                            // Toggle visibility of the clicked dataset
                            dataset.hidden = !dataset.hidden;
                            const visibleDatasets = chart.data.datasets.filter(dataset => !dataset.hidden);
                            
                            // Recalculate min and max Y values for visible datasets
                            const { minY, maxY } = getInitialMinMaxYDualAxis2(visibleDatasets, uniqueParameterIds);
                    
                            // Update y-axes min and max
                            Object.keys(chart.options.scales).forEach(scale => {
                                if (scale !== 'x' && minY[scale] !== undefined && maxY[scale] !== undefined) {
                                    chart.options.scales[scale].min = minY[scale];
                                    chart.options.scales[scale].max = maxY[scale];
                                }
                            });
                        } else {
                            // console.log("onClick Single Chart");
                    
                            const index = legendItem.datasetIndex;
                            const dataset = chart.data.datasets[index];
                    
                            // Toggle visibility of the clicked dataset
                            dataset.hidden = !dataset.hidden;
                    
                            // Recalculate min and max Y values for all datasets
                            const { minY, maxY } = getInitialMinMaxY(chart.data.datasets);
                    
                            // Update y-axis min and max
                            chart.options.scales.y.min = minY;
                            chart.options.scales.y.max = maxY;
                        }
                    
                        // Update the chart after modifications
                        chart.update(datasets);
                    }                    
                },
                beforeUpdate: function(chart) {
                    // Check if there are multiple unique parameter IDs
                    if (uniqueParameterIds.length > 1) {
                        // console.log("beforeUpdate Dual Chart");
                
                        // Filter visible datasets
                        const visibleDatasets = chart.data.datasets.filter(dataset => !dataset.hidden);
                
                        // Recalculate min and max Y values for visible datasets
                        const { minY, maxY } = getInitialMinMaxYDualAxis2(visibleDatasets, uniqueParameterIds);
                
                        // Update y-axes min and max for each scale
                        Object.keys(chart.options.scales).forEach(scaleKey => {
                            if (scaleKey !== 'x' && minY[scaleKey] !== undefined && maxY[scaleKey] !== undefined) {
                                chart.options.scales[scaleKey].min = minY[scaleKey];
                                chart.options.scales[scaleKey].max = maxY[scaleKey];
                            }
                        });
                    } else {
                        // console.log("beforeUpdate Single Chart");
                
                        // Filter visible datasets
                        const visibleDatasets = chart.data.datasets.filter(dataset => !dataset.hidden);
                
                        // Recalculate min and max Y values for visible datasets
                        const { minY, maxY } = getInitialMinMaxY(visibleDatasets);
                
                        // Update y-axis min and max
                        chart.options.scales.y.min = minY;
                        chart.options.scales.y.max = maxY;
                    }
                
                    // Log the updated minY and maxY values for debugging
                    // console.log('Updated minY:', minY);
                    // console.log('Updated maxY:', maxY);
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

// Function to calculate initial min and max Y values from visible datasets
function getInitialMinMaxY(datasets) {
    let minY = Infinity; // Initialize minY to the highest possible number
    let maxY = -Infinity; // Initialize maxY to the lowest possible number

    // console.log("datasets @ getInitialMinMaxY: ", datasets);

    // Log initial minY and maxY before adjustments
    // console.log('Before adjustments:');
    // console.log('Initial minY:', minY);
    // console.log('Initial maxY:', maxY);

    // Arrays to store all yValues for calculating min and max
    let allYValues = [];

    // Iterate over each dataset
    datasets.forEach((dataset, datasetIndex) => {
        if (dataset.hidden) {
            // Skip hidden datasets
            // console.log(`Dataset ${datasetIndex} is hidden, skipping...`);
            return;
        }

        // console.log(`Processing dataset ${datasetIndex}, parameter_id: ${dataset.parameter_id}`);

        // Iterate over each data point in the dataset
        dataset.data.forEach((dataPoint, dataIndex) => {
            if (dataPoint.y !== null && dataPoint.y !== undefined) {
                const yValue = parseFloat(dataPoint.y); // Parse the y value as a float
                if (!isNaN(yValue)) {
                    // Only add valid y values to the array
                    allYValues.push(yValue);
                    // console.log(`Dataset ${datasetIndex}, data point ${dataIndex}: yValue = ${yValue}`);
                } else {
                    // Log a warning if an invalid y value is encountered
                    console.warn(`Invalid yValue encountered in dataset ${datasetIndex}, data point ${dataIndex}:`, dataPoint.y);
                }
            } else {
                // Log a warning if a null or undefined y value is encountered
                console.warn(`Null or undefined yValue encountered in dataset ${datasetIndex}, data point ${dataIndex}:`, dataPoint.y);
            }
        });
    });


    // If no valid yValues found, return early with default min and max
    if (allYValues.length === 0) {
        console.error('No valid yValues found in the datasets.');
        return { minY, maxY };
    }

    // Find min and max yValues from the collected array
    const minDataY = Math.min(...allYValues);
    const maxDataY = Math.max(...allYValues);

    // console.log('minDataY:', minDataY);
    // console.log('maxDataY:', maxDataY);

    // Adjust minY and maxY based on the parameter_id and calculated minDataY and maxDataY
    if (datasets[0].parameter_id === "Stage" || datasets[0].parameter_id === "Elev") {
        // Specific adjustments for "Stage" or "Elev" parameter
        if (minDataY <= 0) {
            minY = minDataY - 1;
            maxY = maxDataY + 1;
        } else if (0 < minDataY < 900) {
            minY = minDataY - 1;
            maxY = maxDataY + 1;
        } else {
            minY = minDataY - (minDataY * 0.1);
            maxY = maxDataY + (maxDataY * 0.1);
        }
    } else if (datasets[0].parameter_id === "Flow") {
        // Specific adjustments for "Flow" parameter
        if (minDataY <= 0) {
            minY = 0;
            // console.log("Flow parameter: minDataY <= 0, setting minY to 0");
        } else if (minDataY > 0 && minDataY <= 10) {
            minY = 0;
            // console.log("Flow parameter: minDataY > 0 && minDataY <= 10, setting minY to 0");
        } else if (minDataY > 10 && minDataY <= 50) {
            minY = Math.round(minDataY) - 2;
            // console.log("Flow parameter: minDataY > 10 && minDataY <= 50, setting minY to", minY);
        } else if (minDataY > 50000) {
            minY = (Math.round(minDataY / 1000) * 1000) - 5000;
            // console.log("Flow parameter: minDataY > 50000, setting minY to", minY);
        } else if (minDataY > 100000) {
            minY = (Math.round(minDataY / 1000) * 1000) - 10000;
            // console.log("Flow parameter: minDataY > 100000, setting minY to", minY);
        } else {
            minY = minDataY - (minDataY * 0.05);
            // console.log("Flow parameter: default case, setting minY to", minY);
        }

        if (maxDataY > 0 && maxDataY <= 10) {
            maxY = Math.round(maxDataY) + 5;
            // console.log("Flow parameter: maxDataY > 0 && maxDataY <= 10, setting maxY to", maxY);
        } else if (maxDataY > 10 && maxDataY <= 50) {
            maxY = Math.round(maxDataY) + 5;
            // console.log("Flow parameter: maxDataY > 10 && maxDataY <= 50, setting maxY to", maxY);
        } else if (maxDataY > 50000) {
            maxY = (Math.round(maxDataY / 1000) * 1000) + 5000;
            // console.log("Flow parameter: maxDataY > 50000, setting maxY to", maxY);
        } else if (maxDataY > 100000) {
            maxY = (Math.round(maxDataY / 1000) * 1000) + 10000;
            // console.log("Flow parameter: maxDataY > 100000, setting maxY to", maxY);
        } else {
            maxY = maxDataY + (maxDataY * 0.05);
            // console.log("Flow parameter: default case, setting maxY to", maxY);
        }
    } else {
        // Default adjustments for other parameter_id values
        minY = minDataY - (minDataY * 0.1);
        maxY = maxDataY + (maxDataY * 0.1);
        // console.log("everything else", minY, maxY);
    }

    // Log adjusted minY and maxY after adjustments
    // console.log('After adjustments:');
    // console.log('Calculated minY:', minY);
    // console.log('Calculated maxY:', maxY);

    // Return object with calculated minY and maxY
    return { minY, maxY };
}

// Function to get current data time
function subtractHoursFromDate(date, hoursToSubtract) {
    return new Date(date.getTime() - (hoursToSubtract * 60 * 60 * 1000));
}

// Function to get current data time
function addHoursFromDate(date, hoursToAdd) {
    return new Date(date.getTime() + (hoursToAdd * 60 * 60 * 1000));
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

// Function to create data table
function createTable(data, floodLevel) {
    // Sort data[0].data by 'x' in descending order
    data[0].data.sort((a, b) => b.x - a.x);

    // Initialize the table structure
    let table = '<table id="gage_data"><thead><tr><th>Date Time</th><th>Value</th></tr></thead><tbody>';

    let parameter_id = data[0].parameter_id;
    // console.log('parameter_id @ createTableWithoutFloodLevel: ', parameter_id);

    // Iterate through each point in data[0].data
    data[0].data.forEach(point => {
        // Format the date based on the 'x' value
        const date = new Date(point.x).toLocaleString();

        let formattedValue = null;
        if (point.y !== null) {
            if (parameter_id === "Flow") {
                formattedValue = point.y.toFixed(0);
            } else {
                formattedValue = point.y.toFixed(2);
            }
        } else {
            formattedValue = 'N/A';
        }
        
        // Determine if the current value exceeds the flood level
        // console.log("exceedFloodLevel: ", formattedValue, floodLevel, typeof(formattedValue), typeof(floodLevel));
        const exceedFloodLevel = parseFloat(formattedValue) > floodLevel;
        // console.log("exceedFloodLevel: ", exceedFloodLevel);

        // Construct the table row with conditional class for text color
        table += `<tr>
                    <td>${date}</td>
                    <td style="color: ${exceedFloodLevel ? 'red' : 'inherit'}">${formattedValue}</td>
                  </tr>`;
    });

    // Close the table structure
    table += '</tbody></table>';

    // Return the complete HTML table
    return table;
}

// Function to create data table without floodLevel
function createTableWithoutFloodLevel(data) {
    // console.log('data @ createTableWithoutFloodLevel: ', data);

    // Sort data[0].data by 'x' in descending order
    data[0].data.sort((a, b) => b.x - a.x);

    // Initialize the table structure
    let table = '<table id="gage_data"><thead><tr><th>Date Time</th><th>Value</th></tr></thead><tbody>';

    let parameter_id = data[0].parameter_id;
    // console.log('parameter_id @ createTableWithoutFloodLevel: ', parameter_id);

    // Iterate through each point in data[0].data
    data[0].data.forEach(point => {
        // Format the date based on the 'x' value
        const date = new Date(point.x).toLocaleString();

        let formattedValue = null;
        if (point.y !== null) {
            if (parameter_id === "Flow") {
                formattedValue = point.y.toFixed(0);
            } else {
                formattedValue = point.y.toFixed(2);
            }
        } else {
            formattedValue = 'N/A';
        }

        // Construct the table row
        table += `<tr>
                    <td>${date}</td>
                    <td>${formattedValue}</td>
                  </tr>`;
    });

    // Close the table structure
    table += '</tbody></table>';

    // Return the complete HTML table
    return table;
}

// Function to get flood level
function getFloodLevel(floodLevelTimeSeries) {
    let floodLevel = null;

    // Check if floodLevelTimeSeries is not empty and the first element has a 'y' property
    if (floodLevelTimeSeries.length > 0 && floodLevelTimeSeries[0].hasOwnProperty('y')) {
        // Extract the y value from the first row
        floodLevel = floodLevelTimeSeries[0].y;

        // Check if floodLevel is null or undefined, set it to negative infinity
        if (floodLevel === null || floodLevel === undefined) {
            floodLevel = Infinity;
        }

        // Log the floodLevel value
        // console.log("floodLevel:", floodLevel); // Output: 24.999999999999996 or -Infinity if initially null/undefined
    } else {
        // console.log("floodLevelTimeSeries is empty or does not contain 'y' property in the first row.");
    }

    return floodLevel;
}

// Function to get minY and maxY for dual axis chart
function getInitialMinMaxYDualAxis(datasets, uniqueParameterIds) {
    // console.log("getInitialMinMaxYDualAxis for dual axis");

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

    // console.log("Initial minY:", minY);
    // console.log("Initial maxY:", maxY);
    // console.log("Initial allYValuesY0:", allYValuesY0);
    // console.log("Initial allYValuesY1:", allYValuesY1);

    datasets.forEach((dataset, datasetIndex) => {
        // console.log(`Processing dataset ${datasetIndex}:`, dataset);

        if (dataset.hidden) {
            // console.log(`Dataset ${datasetIndex} is hidden, skipping.`);
            return; // Skip hidden datasets
        }

        const parameterIndex = uniqueParameterIds.indexOf(dataset.parameter_id);
        // console.log(`parameterIndex for dataset ${datasetIndex}:`, parameterIndex);

        dataset.data.forEach((dataPoint, dataPointIndex) => {
            const yValue = parseFloat(dataPoint.y);
            // console.log(`Data point ${dataPointIndex} (yValue: ${yValue}) in dataset ${datasetIndex}`);

            if (parameterIndex === 0) {
                allYValuesY0.push(yValue); // Collect yValues for y0 axis
                // console.log(`Added ${yValue} to allYValuesY0`);
            } else if (parameterIndex === 1) {
                allYValuesY1.push(yValue); // Collect yValues for y1 axis
                // console.log(`Added ${yValue} to allYValuesY1`);
            }
        });
    });

    // console.log("Final allYValuesY0:", allYValuesY0);
    // console.log("Final allYValuesY1:", allYValuesY1);

    // Find min and max yValues for y0 axis
    if (allYValuesY0.length > 0) {
        minY.y0 = Math.min(...allYValuesY0);
        maxY.y0 = Math.max(...allYValuesY0);
        // console.log('Updated minY.y0:', minY.y0);
        // console.log('Updated maxY.y0:', maxY.y0);
    }

    // Find min and max yValues for y1 axis
    if (allYValuesY1.length > 0) {
        minY.y1 = Math.min(...allYValuesY1);
        maxY.y1 = Math.max(...allYValuesY1);
        // console.log('Updated minY.y1:', minY.y1);
        // console.log('Updated maxY.y1:', maxY.y1);
    }

    // Adjust minY and maxY based on the axis-specific logic if needed

    // console.log('Final minY:', minY);
    // console.log('Final maxY:', maxY);

    // Return object with calculated minY and maxY for both y0 and y1 axes
    return { minY, maxY };
}

// Function to display vertical datum and elevation
function displayLocationData(data, data2, versionId) {
    let verticalDatum = null;
    let elevation = null;
    if (versionId === "29") {
        verticalDatum = data["vertical-datum"];
        elevation = (data["elevation"] - data2["constant-value"]).toFixed(2);
    } else {
        verticalDatum = data["vertical-datum"];
        elevation = (data["elevation"]).toFixed(2);
    }

    const locationDataDiv = document.getElementById("gage_control_04");
    locationDataDiv.innerHTML += `Vertical Datum: ${verticalDatum}<br>Gage Zero: ${elevation} ft` + `<br>`;
}

// Function to display ngvd29 vertical datum
function displayNgvd29Data(data, data2, versionId) {

    let verticalDatum = null;
    let elevation = null;
    if (versionId === "29") {
        verticalDatum = data["location-level-id"].slice(-6);
        elevation = (data["constant-value"] - data["constant-value"]).toFixed(2);
    } else {
        if (data["constant-value"] < 900) {
            verticalDatum = data["location-level-id"].slice(-6);
            elevation = (data["constant-value"]).toFixed(2);
        } else {
            verticalDatum = data["location-level-id"].slice(-6);
            elevation = "N/A";
        }
    }

    const locationDataDiv = document.getElementById("gage_control_04");
    locationDataDiv.innerHTML += `Vertical Datum: ${verticalDatum}<br>Gage Zero: ${elevation} ft`;
}

// Function to generate formatted time string for a given date
function format6AmTargetTime(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are 0-based
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}T06:00:00-05:00`;
}

// Function to find value at 6:00 AM from the datasets
function findValueAt6Am(datasets, targetTimeString) {
    // console.log("datasets @ findValueAt6Am ", datasets);

    // Convert target time to UTC
    const targetDate = new Date(targetTimeString);
    const targetTimeUTC = targetDate.getTime();

    // Extract parameterId from name
    const name = datasets[0]?.name; // assuming name is consistent across datasets
    const splitName = name.split('.');
    const parameterId = splitName[1];

    // Helper function to format date to 'mm-dd-yyyy hh24mi'
    function formatDate(date) {
        const mm = String(date.getMonth() + 1).padStart(2, '0');
        const dd = String(date.getDate()).padStart(2, '0');
        const yyyy = date.getFullYear();
        const hh = String(date.getHours()).padStart(2, '0');
        const mi = String(date.getMinutes()).padStart(2, '0');
        return `${mm}-${dd}-${yyyy} ${hh}${mi}`;
    }

    // Helper function to add comma as thousand separator
    function addThousandSeparator(value) {
        return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    // Iterate through datasets to find the corresponding value
    for (let dataset of datasets) {
        if (dataset.values && dataset.values.length > 0) {
            const values = dataset.values;
            const unit_id = dataset.units;
            const result = values.find(entry => entry[0] === targetTimeUTC);
            if (result && result[1] != null) {
                const formattedTime = formatDate(targetDate);
                let valueFormatted;

                if (parameterId === "Stage" || parameterId === "Precip") {
                    valueFormatted = result[1].toFixed(2);
                } else if (parameterId === "Flow") {
                    if (result[1] > 20) {
                        valueFormatted = addThousandSeparator((Math.round(result[1] * 10) / 10).toFixed(0));
                    } else {
                        valueFormatted = addThousandSeparator(result[1].toFixed(0));
                    }
                } else {
                    valueFormatted = result[1].toFixed(2);
                }

                return `${formattedTime} = ${valueFormatted} ${unit_id}`;
            }
        }
    }

    const formattedTime = formatDate(targetDate);
    return `No value found for ${formattedTime} US/Central time.`;
}

// Main function to handle the process
function processDataAndDisplay(datasets) {
    // Check if there are multiple datasets
    if (datasets.length > 1) {
        document.getElementById('gage_control_03').innerHTML = "data not available for multiple time series";
        return;
    }
    // Get today's date
    const today = new Date();

    // Get yesterday's date
    const yesterday = new Date(today);
    yesterday.setDate(today.getDate() - 1);

    // Construct the target time strings for 6:00 AM US/Central time for today and yesterday
    const targetTimeStringToday = format6AmTargetTime(today);
    const targetTimeStringYesterday = format6AmTargetTime(yesterday);

    // Find values at the target times
    const valueToday = findValueAt6Am(datasets, targetTimeStringToday);
    const valueYesterday = findValueAt6Am(datasets, targetTimeStringYesterday);

    // Display the results in the <div> with id "gage_control_03"
    document.getElementById('gage_control_03').innerHTML = `
        ${valueToday}<br>
        ${valueYesterday}
    `;
}

// Function to get minY and maxY for dual axis chart version 2
function getInitialMinMaxYDualAxis2(datasets, uniqueParameterIds) {
    let minY = { y0: Infinity, y1: Infinity };
    let maxY = { y0: -Infinity, y1: -Infinity };

    datasets.forEach((dataset, datasetIndex) => {
        if (!dataset.data || !Array.isArray(dataset.data)) {
            // console.log(`Dataset ${datasetIndex} has no valid data array.`);
            return;
        }

        dataset.data.forEach((point, pointIndex) => {
            if (typeof point.y !== 'number') {
                // console.log(`Point ${pointIndex} in dataset ${datasetIndex} has an invalid y value: ${point.y}`);
                return;
            }

            const yAxisID = dataset.yAxisID;
            if (yAxisID === uniqueParameterIds[0]) {
                if (point.y < minY.y0) {
                    minY.y0 = point.y;
                    // console.log(`Updated minY.y0 at dataset ${datasetIndex}, point ${pointIndex}: ${minY.y0}`);
                }
                if (point.y > maxY.y0) {
                    maxY.y0 = point.y;
                    // console.log(`Updated maxY.y0 at dataset ${datasetIndex}, point ${pointIndex}: ${maxY.y0}`);
                }
            } else if (yAxisID === uniqueParameterIds[1]) {
                if (point.y < minY.y1) {
                    minY.y1 = point.y;
                    // console.log(`Updated minY.y1 at dataset ${datasetIndex}, point ${pointIndex}: ${minY.y1}`);
                }
                if (point.y > maxY.y1) {
                    maxY.y1 = point.y;
                    // console.log(`Updated maxY.y1 at dataset ${datasetIndex}, point ${pointIndex}: ${maxY.y1}`);
                }
            } else {
                // console.log(`Dataset ${datasetIndex}, point ${pointIndex} has an invalid yAxisID: ${yAxisID}`);
            }
        });
    });

    // Set to null if no valid values were found
    if (minY.y0 === Infinity) minY.y0 = null;
    if (minY.y1 === Infinity) minY.y1 = null;
    if (maxY.y0 === -Infinity) maxY.y0 = null;
    if (maxY.y1 === -Infinity) maxY.y1 = null;

    // console.log('Final minY:', minY);
    // console.log('Final maxY:', maxY);

    return { minY, maxY };
}