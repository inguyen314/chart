document.addEventListener('DOMContentLoaded', function () {
    // Display the loading indicator
    const loadingIndicator = document.getElementById('loading');
    loadingIndicator.style.display = 'block';

    // Define your tsids
    const tsids = [
        { cwms_ts_id: encodeURIComponent(cwms_ts_id)},
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_2)},
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_3)},
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_4)},
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_5)},
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_6)},
        { cwms_ts_id: encodeURIComponent(cwms_ts_id_7)}
    ];
    // console.log("tsids = ", tsids);

    // Filter out tsids where cwms_ts_id is null or undefined
    const validTsids = tsids.filter(data => data.cwms_ts_id !== null && data.cwms_ts_id !== undefined && data.cwms_ts_id !== 'null');
    // console.log("validTsids = ", validTsids);

    // Get current date and time
    const currentDateTime = new Date();
    // console.log("currentDateTime = ", currentDateTime);

    // Subtract thirty hours from current date and time
    const currentDateTimeMinusLookBack = subtractHoursFromDate(currentDateTime, lookback);
    // console.log("currentDateTimeMinusLookBack = ", currentDateTimeMinusLookBack);

     // Add thirty hours from current date and time
     const currentDateTimeAddLookForward = addHoursFromDate(currentDateTime, lookforward);
     // console.log("currentDateTimeAddLookForward = ", currentDateTimeAddLookForward);

    let cdaUrl = null;
    if (cda === "public") {
        cdaUrl = "https://cwms-data.usace.army.mil/cwms-data";
    } else if (cda === "internal") {
        cdaUrl = "https://wm.mvs.ds.usace.army.mil:8243/mvs-data";
    } else {
        cdaUrl = null;
    }

    // Map each dataset to its corresponding URL
    const timeseriesUrl = validTsids.map(data => {
        const queryString = data.cwms_ts_id; // Assuming this is correct
        return `${cdaUrl}/timeseries?name=${queryString}&begin=${currentDateTimeMinusLookBack.toISOString()}&end=${currentDateTimeAddLookForward.toISOString()}&office=${office}`;
    });
    // console.log("timeseriesUrl = ", timeseriesUrl);

    // Fetch all tsids simultaneously
    Promise.all(
        timeseriesUrl.map(url =>
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
        // console.log('datasets:', datasets);

        const nonEmptyDatasets = datasets.filter(data => data.values && data.values.length > 0);
        // console.log('nonEmptyDatasets:', nonEmptyDatasets);

        // First Location
        const firstDataset = nonEmptyDatasets[0];
        // console.log('firstDataset:', firstDataset);

        const values = nonEmptyDatasets[0].values;
        const dateTimes = (values.map(item => item[0])); // const dateTimes = (values.map(item => item[0])).map(formatDate); 
        // console.log('dateTimes:', dateTimes);

        const cwmsTsId = firstDataset.name;
        const unitId = firstDataset.units;
        const timeZone = firstDataset['time-zone'];
        const nameParts = firstDataset.name.split('.');
        const locationId = nameParts[0];
        const parameterId = nameParts[1];
        const versionId = nameParts[5];
        // console.log("locationId: ", locationId);  // St Louis-Mississippi
        // console.log("parameterId: ", parameterId);  // Stage
        // console.log("versionId: ", versionId);  // lrgsShef-rev

        // Second Location
        let parameterId2, unitId2 = null;
        if (nonEmptyDatasets.length > 1) {
            const secondDataset = nonEmptyDatasets[1];
            // console.log('secondDataset:', secondDataset);
            const values2 = nonEmptyDatasets[1].values;
            const dateTimes2 = (values2.map(item => item[0])).map(formatDate); // Adjusted to use formatDate function
            // console.log('dateTimes2:', dateTimes2);

            const cwmsTsId2 = secondDataset.name;
            unitId2 = secondDataset.units;
            const nameParts2 = secondDataset.name.split('.');
            const locationId2 = nameParts2[0];
            parameterId2 = nameParts2[1];
            // console.log("locationId2: ", locationId2);  // St Louis-Mississippi
            // console.log("parameterId2: ", parameterId2);  // Stage
            // console.log("unitId2: ", unitId2);  // ft
        }

        // Chart Type Setup
        if (nonEmptyDatasets.length === 1 & (parameterId === "Stage" || parameterId === "Elev")) {
            // console.log("============== for single dataset plot with location levels ==============");

            const levelIdFlood = locationId + ".Stage.Inst.0.Flood";
            // console.log(levelIdFlood);
            const levelIdHingeMin = locationId + ".Height.Inst.0.Hinge Min";
            // console.log(levelIdHingeMin);
            const levelIdHingeMax = locationId + ".Height.Inst.0.Hinge Max";
            // console.log(levelIdHingeMax);
            const levelIdLwrp = locationId + ".Stage.Inst.0.LWRP";
            // console.log(levelIdLwrp);
            const levelIdNgvd29 = locationId + ".Height.Inst.0.NGVD29";
            // console.log(levelIdLwrp);
            const levelIdEffectiveDate = "2024-01-01T08:00:00";

            // Define the URLs to fetch related data from
            const url1 = `https://water.usace.army.mil/cwms-data/levels/${levelIdFlood}?office=${office}&effective-date=${levelIdEffectiveDate}&unit=ft`;
            const url2 = `https://water.usace.army.mil/cwms-data/levels/${levelIdHingeMin}?office=${office}&effective-date=${levelIdEffectiveDate}&unit=ft`;
            const url3 = `https://water.usace.army.mil/cwms-data/levels/${levelIdHingeMax}?office=${office}&effective-date=${levelIdEffectiveDate}&unit=ft`;
            const url4 = `https://water.usace.army.mil/cwms-data/levels/${levelIdLwrp}?office=${office}&effective-date=${levelIdEffectiveDate}&unit=ft`;
            const url5 = `https://water.usace.army.mil/cwms-data/locations/${locationId}?office=${office}`;
            const url6 = `https://water.usace.army.mil/cwms-data/levels/${levelIdNgvd29}?office=${office}&effective-date=${levelIdEffectiveDate}&unit=ft`;
            // console.log('url1:', url1);
            // console.log('url2:', url2);
            // console.log('url3:', url3);
            // console.log('url4:', url4);
            // console.log('url5:', url5);
            // console.log('url6:', url6);

            // Fetch the related data
            Promise.all([
                fetch(url1).then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                }).catch(error => {
                    // console.error('Error fetching data from url1:', error);
                    return null; // Return null if fetch failed
                }),
            
                fetch(url2)
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 404) {
                            // Handle 404 specifically
                            console.error('Error 404: Hinge Min resource not found');
                            return null; // Return null if the resource is not found
                        }
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('Error fetching data from url2:', error);
                    return null; // Return null if fetch failed for any reason
                }),
            
                fetch(url3)
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 404) {
                            // Handle 404 specifically
                            console.error('Error 404: Hinge Max resource not found');
                            return null; // Return null if the resource is not found
                        }
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('Error fetching data from url3:', error);
                    return null; // Return null if fetch failed for any reason
                }),

                fetch(url4)
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 404) {
                            // Handle 404 specifically
                            console.error('Error 404: LWRP resource not found');
                            return null; // Return null if the resource is not found
                        }
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('Error fetching data from url4:', error);
                    return null; // Return null if fetch failed for any reason
                }),


                fetch(url5).then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                }).catch(error => {
                    // console.error('Error fetching data from url5:', error);
                    return null; // Return null if fetch failed
                }),
                fetch(url6).then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                }).catch(error => {
                    // console.error('Error fetching data from url6:', error);
                    return null; // Return null if fetch failed
                })
            ])
            .then(metaDataArray => {
                // console.log('metaDataArray:', metaDataArray);
                
                const floodLevelData = metaDataArray[0];
                const hingeMinData = metaDataArray[1];
                const hingeMaxData = metaDataArray[2];
                const lwrpData = metaDataArray[3];
                const locationData = metaDataArray[4];
                const ngvd29Data = metaDataArray[5];

                const floodLevelTimeSeries = dateTimes.map(dateTime => {
                    // Check if floodLevelData contains a valid constant-level
                    if (floodLevelData && floodLevelData["constant-value"] !== undefined) {
                        // Set constant-value to null if it's greater than 900
                        const constantValue = floodLevelData["constant-value"] > 900 ? null : floodLevelData["constant-value"];
                        return {
                            x: dateTime,
                            y: constantValue
                        };
                    } else {
                        // Handle case where constant-level is not found or not valid
                        return {
                            x: dateTime,
                            y: null // or any default value you want to assign
                        };
                    }
                });
                // console.log("floodLevelTimeSeries: ", floodLevelTimeSeries);

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
                // console.log("hingeMinTimeSeries: ", hingeMinTimeSeries);

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
                // console.log("hingeMaxTimeSeries: ", hingeMaxTimeSeries);

                const lwrpTimeSeries = dateTimes.map(dateTime => {
                    // Check if lwrpData contains a valid constant-level
                    if (lwrpData && lwrpData["constant-value"] !== undefined) {
                        // Set constant-value to null if it's greater than 900
                        const constantValue = lwrpData["constant-value"] > 900 ? null : lwrpData["constant-value"];
                        return {
                            x: dateTime,
                            y: constantValue
                        };
                    } else {
                        // Handle case where constant-level is not found or not valid
                        return {
                            x: dateTime,
                            y: null // or any default value you want to assign
                        };
                    }
                });
                // console.log("lwrpTimeSeries: ", lwrpTimeSeries);

                const series = [
                    {
                        label: cwmsTsId,
                        parameter_id: parameterId,
                        unit_id: unitId,
                        time_zone: timeZone,
                        data: firstDataset.values.map(item => ({ x: item[0], y: item[1] })),
                        borderColor: 'red',
                        backgroundColor: 'red',
                        // Toggle Button
                        // borderWidth: 4, // Change the width of the connecting lines
                        tension: 0.5, // Adjust this value for the desired curve. 0: Represents straight lines. 1: Represents very smooth, rounded curves.
                        // cubicInterpolationMode: 'default', // Set to 'default' for a solid and smooth line. 
                            // default (default): This is the default cubic interpolation mode. It uses a single cubic Bezier curve to connect data points.
                            // monotone: This mode creates a single cubic Bezier curve that is guaranteed to be monotone (non-increasing or non-decreasing) between data points. This can be useful when dealing with data that has natural trends.
                            // stepped: This mode connects data points with horizontal and vertical lines, creating a stepped appearance. It doesn't use curves and is suitable for step-like data.
                        // pointRadius: 1, // Set pointRadius to 0 to hide data point dots
                        // showLine: true, // Show the connecting line
                        // pointBackgroundColor: 'rgba(0, 0, 255, 1)', // Data point dot color (blue in this example)
                        hoverBackgroundColor: 'rgba(0, 0, 255, 1)', // blue hoverBackgroundColor and hoverBorderColor: These parameters let you define the background and border colors when a user hovers over a chart element.
                        // hoverBorderColor: 'rgba(0, 255, 0, 1)', // green 
                        // hoverBorderWidth: 1,// Controls the border width when hovering over a chart element.
                        fill: false
                    },
                    {
                        label: 'Flood Level',
                        data: floodLevelTimeSeries,
                        borderColor: 'blue',
                        backgroundColor: 'blue',
                        fill: false,
                        borderWidth: 3, // Change the width of the connecting lines
                        pointRadius: 0.0, // Set pointRadius to 0 to hide data point dots
                        hidden: true // Initially hidden
                    },
                    hingeMinData !== null && {
                        label: 'Hinge Min',
                        data: hingeMinTimeSeries,
                        borderColor: 'black',
                        backgroundColor: 'black',
                        fill: false,
                        borderWidth: 3, // Change the width of the connecting lines
                        pointRadius: 0.0, // Set pointRadius to 0 to hide data point dots
                        hidden: true // Initially hidden
                    },
                    hingeMaxData !== null && {
                        label: 'Hinge Max',
                        data: hingeMaxTimeSeries,
                        borderColor: 'black',
                        backgroundColor: 'black',
                        fill: false,
                        borderWidth: 3, // Change the width of the connecting lines
                        pointRadius: 0.0, // Set pointRadius to 0 to hide data point dots
                        hidden: true // Initially hidden
                    },
                    lwrpData !== null && {
                        label: 'LWRP',
                        data: lwrpTimeSeries,
                        borderColor: 'black',
                        backgroundColor: 'black',
                        fill: false,
                        borderWidth: 3, // Change the width of the connecting lines
                        pointRadius: 0.0, // Set pointRadius to 0 to hide data point dots
                        hidden: true // Initially hidden
                    }
                ].filter(series => series);

                // console.log("series: ", (series));

                // Create Chart JS
                plotData(series);

                // Get flood level
                const floodLevel = getFloodLevel(floodLevelTimeSeries);

                // Create Data Table
                document.getElementById('data_table').innerHTML = createTable(series, floodLevel); // floodLevelTimeSeries[0].y

                // Location Data
                // console.log("locationData: ", locationData);
                // Call the function with the locationData object
                displayLocationData(locationData, ngvd29Data, versionId);

                // NGVD29 Data
                // console.log("ngvd29Data: ", ngvd29Data);
                // Call the function with the locationData object
                displayNgvd29Data(ngvd29Data, locationData, versionId);

                // Call the main function to process and display 6am data
                processDataAndDisplay(nonEmptyDatasets);

                loadingIndicator.style.display = 'none';
            })
            .catch(error => {
                console.error('Error fetching related data:', error);
                loadingIndicator.style.display = 'none';
            });
        } else if (nonEmptyDatasets.length === 1) {
            // console.log("============== for plotting flow, precip and water quality ==============");

                const series = [
                    {
                        label: cwmsTsId,
                        parameter_id: parameterId,
                        unit_id: unitId,
                        time_zone: timeZone,
                        data: firstDataset.values.map(item => ({ x: item[0], y: item[1] })), // Mapping values to x and y properties
                        borderColor: 'red',
                        backgroundColor: 'red',
                        fill: false
                    }
                ].filter(series => series);

                // // console.log(series);

                plotData(series);

                 // Create Data Table
                 document.getElementById('data_table').innerHTML = createTableWithoutFloodLevel(series);

                 // Call the main function to process and display 6am data
                processDataAndDisplay(nonEmptyDatasets);

                // No metadata
                document.getElementById("gage_control_04").innerText = "Data available for single stage time series.";

                loadingIndicator.style.display = 'none';
            
        } else if (nonEmptyDatasets.length > 1 & (parameterId === parameterId2)) {
            // console.log("============== for multiple dataset plots with the same parameter id ==============");

            // More than one dataset has data, plot only the datasets
            const colors = [
                'rgba(255, 99, 132, 1)',   // Red
                'rgba(54, 162, 235, 1)',   // Blue
                'rgba(0, 128, 0, 1)',      // Green
                'rgba(153, 102, 255, 1)',  // Purple
                'rgba(255, 159, 64, 1)',   // Orange
                'rgba(255, 206, 86, 1)',   // Yellow
                'rgba(169, 169, 169, 1)'   // Darker Gray
            ];
            

            const series = nonEmptyDatasets.map((data, index) => {
                const cwmsTsId = data.name; // Retrieve cwmsTsId from each dataset
        
                return {
                    label: cwmsTsId, // Unique label for each dataset
                    parameter_id: parameterId,
                    unit_id: unitId,
                    time_zone: timeZone,
                    data: data.values.map(item => ({ x: item[0], y: item[1] })),
                    borderColor: colors[index % colors.length],
                    backgroundColor: colors[index % colors.length],
                    fill: false
                };
            });

            // console.log(series);

            // Plot Data
            plotData(series);

            // Call the main function to process and display 6am data
            processDataAndDisplay(nonEmptyDatasets);

            // No metadata
            document.getElementById("gage_control_04").innerText = "Data available for single stage time series.";

            loadingIndicator.style.display = 'none';
        } else if (nonEmptyDatasets.length > 1 & (parameterId !== parameterId2)) {
            // console.log("===== For multiple line plots where parameter id are NOT the same =====");
        
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
                // Extracting dataset details
                const cwmsTsId = data.name; // The name of the dataset
                const parameterId = data.name.split('.')[1]; // The parameter ID from the dataset name
                const unitId = data.units; // The unit ID
            
                // Formatting the data points
                const formattedData = data.values.map(item => ({ x: item[0], y: item[1] }));
            
                // Returning the series object
                return {
                    label: cwmsTsId,
                    parameter_id: parameterId,
                    unit_id: unitId,
                    data: formattedData,
                    borderColor: colors[index % colors.length], // Cycling through colors
                    backgroundColor: colors[index % colors.length], // Cycling through colors
                    fill: false, // Not filling the area under the line
                    yAxisID: parameterId // Linking to the Y-axis
                };
            });
            
            // console.log("series: ", series);

            // Plot Data
            plotData(series);

            // Call the main function to process and display 6am data
            processDataAndDisplay(nonEmptyDatasets);

            loadingIndicator.style.display = 'none';
        } else {
            // console.log('No valid datasets to display.');
            loadingIndicator.style.display = 'none';
        }
    })
    .catch(error => {
        console.error('Error fetching data:', error);
        loadingIndicator.style.display = 'none';
    });
});