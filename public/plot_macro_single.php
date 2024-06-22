<?php 
require_once('../private/initialize.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set("xdebug.var_display_max_children", '-1');
ini_set("xdebug.var_display_max_data", '-1');
ini_set("xdebug.var_display_max_depth", '-1');

date_default_timezone_set('America/Chicago');
if (date_default_timezone_get()) {
    //echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br />';
}
if (ini_get('date.timezone')) {
    //echo 'date.timezone: ' . ini_get('date.timezone');
}

$set_options = set_options2($db);

// GET REQUEST 
    $basin = $_GET['basin'] ?? null;
    $cwms_ts_id = $_GET['cwms_ts_id'] ?? 'St Louis-Mississippi.Stage.Inst.30Minutes.0.lrgsShef-rev';
    $start_day = $_GET['start_day'] ?? '4';
    $end_day = $_GET['end_day'] ?? '0';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>St. Louis District Home Page</title>
        <meta name="Description" content="U.S. Army Corps of Engineers St. Louis District Home Page" />
        <link rel="stylesheet" href="../../../css/body.css" />
        <link rel="stylesheet" href="../../../css/breadcrumbs.css" />
        <link rel="stylesheet" href="../../../css/jumpMenu.css" />
        <script type="text/javascript" src="../../../js/main.js"></script>

        <!-- Additional CSS -->
        <link rel="stylesheet" href="../../../css/rebuild.css" />

        <!-- Include Moment.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
        <!-- Include the Chart.js library -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <!-- Include the Moment.js adapter for Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>

        <link href="stylesheets/style.css" rel="stylesheet" type="text/css" media="all" />

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    </head>
    <body>
        <div id="page-container">
            <header id="header">
                <!--Header content populated here by JavaScript Tag at end of body -->
            </header>
            <div class="page-wrap">
                <div class="container-fluid">
                    <div id="breadcrumbs">
                    </div>
                    <div class="page-content">
                        <sidebar id="sidebar">
                            <!--Side bar content populated here by JavaScript Tag at end of body -->
                        </sidebar>
                        <div id="topPane" class="col-md backend-cp-collapsible">
                        <!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->

                        <!--APP START-->
                        <div style="width: 1000px; height: 600px;"><canvas id="lineChart"></canvas></div>

                        <?php
                        // GET TIME SERIES 1
                            $table_data = get_table_data($db, $cwms_ts_id, $start_day, $end_day); // Array ( [0] => stdClass Object
                            //print_r($table_data);

                            $table_data_json = json_encode($table_data); // JSON string
                            //print_r($table_data_json);

                            $data = json_decode($table_data_json, true); // Array ( [0] => Array

                            $cwms_ts_id = array_column($data, 'cwms_ts_id');
                            $location_id = array_column($data, 'location_id');
                            $parameter_id = array_column($data, 'parameter_id');
                            $version_id = array_column($data, 'version_id');
                            $date_time = array_column($data, 'date_time');
                            $value = array_column($data, 'value');
                            $unit = array_column($data, 'unit_id');
                            $quality_code = array_column($data, 'quality_code');

                            $cwms_ts_id_first_element = $cwms_ts_id[0];
                            //print_r($cwms_ts_id_first_element);

                            $location_id_first_element = $location_id[0];
                            //print_r($location_id_first_element);

                            $parameter_id_first_element = $parameter_id[0];
                            //print_r($parameter_id_first_element);

                            $version_id_first_element = $version_id[0];
                            //print_r($version_id_first_element);

                            $unit_id_first_element = $unit[0];
                            //print_r($unit_id_first_element);


                        // JSON string cwms_ts_id
                            $js_cwms_ts_id = json_encode($cwms_ts_id_first_element);


                        // JSON string start_day and end_day
                            $js_start_day = json_encode($start_day);
                            $js_end_day = json_encode($end_day);


                        // JSON string table_data
                            $table_data_json = json_encode($table_data);


                        // Associative array data
                            $data = json_decode($table_data_json, true);
                        ?>

                        <!-- Add an anchor tag for downloading the chart image -->
                        <a id="downloadLink" style="display: none;"></a>

                        <script>
                        // Basin
                            var basin = <?php echo json_encode($basin); ?>;
                            console.log("basin = ", basin);
                        // Time Series 1
                            var cwms_ts_id = <?php echo json_encode($cwms_ts_id); ?>;
                            var location_id = <?php echo json_encode($location_id); ?>;
                            var parameter_id = <?php echo json_encode($parameter_id); ?>;
                            var date_time = <?php echo json_encode($date_time); ?>;
                            var value = <?php echo json_encode($value); ?>;
                            var unit = <?php echo json_encode($unit); ?>;
                            var quality_code = <?php echo json_encode($quality_code); ?>;
                            console.log("cwms_ts_id = ", cwms_ts_id);
                            console.log("location_id = ", location_id);
                            console.log("parameter_id = ", parameter_id);
                            console.log("date_time = ", date_time);
                            console.log("value = ", value);
                            console.log("unit = ", unit);
                            console.log("quality_code = ", quality_code);

                            let firstCwmsTsId = cwms_ts_id[0];
                            console.log("firstCwmsTsId = ",firstCwmsTsId);

                            let firstLocationId = location_id[0];
                            console.log("firstLocationId = ",firstLocationId);

                            let firstParameterId = parameter_id[0];
                            console.log("firstParameterId = ",firstParameterId);

                            const firstValueUnit = unit[0];
                            console.log("firstValueUnit = ",firstValueUnit);


                        // Function: check Daylight Saving Time
                            function isDaylightSavingTime(date) {
                                const january = new Date(date.getFullYear(), 0, 1);
                                const july = new Date(date.getFullYear(), 6, 1);
                                const stdTimezoneOffset = Math.max(january.getTimezoneOffset(), july.getTimezoneOffset());
                                return date.getTimezoneOffset() < stdTimezoneOffset;
                            }


                        // Find Y-Axis Min and Max
                            let roundedMinValueAxis_y1;
                            let roundedMaxValueAxis_y1;

                            // time series 1
                            var filteredValues = value.filter(value => value !== null); // Use filter to remove null values
                            var minValue = Math.min(...filteredValues);
                            var maxValue = Math.max(...filteredValues);
                            console.log("minValue = ", minValue);
                            console.log("maxValue = ", maxValue);
                            
                            // find the min from 7 time series array
                            const minNumber_y1 = Math.min(minValue);
                            console.log("minNumber_y1 = ", minNumber_y1);
                            // find the max from 7 time series array
                            const maxNumber_y1 = Math.max(maxValue);
                            console.log("maxNumber_y1 = ", maxNumber_y1);

                            // set min and max based on parameter_id
                            if (firstParameterId == "Stage" || firstParameterId == "Elev") {
                                console.log("find min and max value for axis for Stage or Elev");
                                // SAME PARAMETER: MIN STAGE
                                if (minNumber_y1 <= 0) { 
                                    var minValueAxis_y1 = minNumber_y1 -1;
                                    console.log("less than 0");
                                } else if (minNumber_y1 <= 10) {
                                    var minValueAxis_y1 = minNumber_y1 - 1;
                                    console.log("less than 10");
                                } else if (minNumber_y1>200) {
                                    console.log("greater than 200");
                                    var minValueAxis_y1 = minNumber_y1 -1;
                                } else {
                                    var minValueAxis_y1 = minNumber_y1 - (minNumber_y1*0.1);
                                    console.log("minus 10%");
                                }
                                console.log("minValueAxis_y1 = ", minValueAxis_y1);
                                // SAME PARAMETER: MAX STAGE
                                if (maxNumber_y1<=0) {
                                    console.log("less than 0");
                                    var maxValueAxis_y1 = maxNumber_y1 + 1;
                                } else if (maxNumber_y1 <= 10) {
                                    console.log("less than 10");
                                    var maxValueAxis_y1 = maxNumber_y1 + 1;
                                } else if (maxNumber_y1 > 200) {
                                    console.log("greater than 200");
                                    var maxValueAxis_y1 = maxNumber_y1 + 1;
                                } else {
                                    var maxValueAxis_y1 = maxNumber_y1 + (maxNumber_y1*0.1);
                                    console.log("plus 10%");
                                }
                                console.log("maxValueAxis_y1 = ", maxValueAxis_y1);
                            } else if (firstParameterId == "Flow") {
                                console.log("find min and max value for axis for Flow");
                                // SAME PARAMETER: MIN FLOW
                                if (minNumber_y1 <= 0) {
                                    var minValueAxis_y1 = 0;
                                    console.log("flow id less and equal to 0");
                                } else if (minNumber_y1 > 0 && minNumber_y1 <= 10) {
                                    var minValueAxis_y1 = 0;
                                    console.log("flow is greater than zero but less or equal to 10");
                                } else if (minNumber_y1 > 10 && minNumber_y1 <= 50) {
                                    var minValueAxis_y1 = Math.round(minNumber_y1) - 2;
                                    console.log("greater than 0 and less than 50");
                                } else if (minNumber_y1 > 50000) {
                                    var minValueAxis_y1 = (Math.round(minNumber_y1/1000)*1000) - 5000;
                                    console.log("greater than 50,000");
                                } else if (minNumber_y1 > 100000){
                                    var minValueAxis_y1 = (Math.round(minNumber_y1/1000)*1000) - 10000;
                                    console.log("greater than 100,000");
                                } else {
                                    var minValueAxis_y1 = minNumber_y1 - ((minNumber_y1*0.1));
                                    console.log("minus 10%");
                                }
                                console.log("minValueAxis_y1 = ", minValueAxis_y1);
                                // SAME PARAMETER: MAX FLOW
                                if (maxNumber_y1 > 0 && maxNumber_y1 <= 10) {
                                    var maxValueAxis_y1 = Math.round(maxNumber_y1) + 5;
                                    console.log("greater and equal to 0 and less than 50");
                                } else if (maxNumber_y1 > 10 && maxNumber_y1 <= 50) {
                                    var maxValueAxis_y1 = Math.round(maxNumber_y1) + 5;
                                    console.log("greater and equal to 0 and less than 50");
                                } else if (maxNumber_y1 > 50000) {
                                    var maxValueAxis_y1 = (Math.round(maxNumber_y1/1000)*1000) + 5000;
                                    console.log("greater than 50,000");
                                } else if (maxNumber_y1 > 100000) {
                                    var maxValueAxis_y1 = (Math.round(maxNumber_y1/1000)*1000) + 10000;
                                    console.log("greater than 100,000");
                                } else {
                                    var maxValueAxis_y1 = maxNumber_y1 + ((maxNumber_y1*0.1)+5);
                                    console.log("plus 10%");
                                }
                                console.log("maxValueAxis_y1 = ", maxValueAxis_y1);
                            } else {
                                console.log("find min and max value for axis for everything else");
                                // SAME PARAMETER: MIN EVERYTHING ELSE
                                var minValueAxis_y1 = minNumber_y1 - ((minNumber_y1*0.1));
                                console.log("minValueAxis_y1 = ", minValueAxis_y1);
                                // SAME PARAMETER: MAX EVERYTHING ELSE
                                var maxValueAxis_y1 = maxNumber_y1 + ((maxNumber_y1*0.1)+1);
                                console.log("maxValueAxis_y1 = ", maxValueAxis_y1);
                            }

                            // SAME PARAMETER: ROUNDED MIN AND MAX Y-AXIS
                            roundedMinValueAxis_y1 = Math.round(minValueAxis_y1);
                            console.log("roundedMinValueAxis_y1 = ", roundedMinValueAxis_y1);
                            roundedMaxValueAxis_y1 = Math.round(maxValueAxis_y1);
                            console.log("roundedMaxValueAxis_y1 = ", roundedMaxValueAxis_y1);


                        // Prepare Start and End Day Variables for javascript
                            var js_start_day = <?php echo $js_start_day; ?>;
                            console.log("js_start_day = ", js_start_day); 

                            var js_end_day = <?php echo $js_end_day; ?>;
                            console.log("js_end_day = ", js_end_day); 

                        // Combine and Merge Date-Time and Value
                            // time series 1
                            const data = date_time.map((element, index) => [element, value[index], unit[index], quality_code[index]]);
                            console.log("combined " + firstLocationId + " = ", data);


                        // Initial visibility state of datasets
                            var dataset1Visible = true;

                        // Create a Chart.js Chart
                            var ctx = document.getElementById('lineChart').getContext('2d');

                            var dataset1 = null; // Default to null


                        // Dataset 1
                            if (value.length > 0) {
                                console.log("value.length = ", value.length);
                                dataset1 = {
                                    label: firstCwmsTsId,
                                    data: value,
                                    yAxisID: 'y', // Assign to the first y-axis
                                    borderColor: 'rgba(255, 0, 0, 1)', // red
                                    backgroundColor: 'rgba(255, 0, 0, 1)', //'rgba(255, 0, 0, 1)' , //red //'rgba(255, 255, 255, 1)', //white
                                    borderWidth: 2,
                                    fill: false,
                                    tension: 1.0, // Adjust this value for the desired curve. 0: Represents straight lines. 1: Represents very smooth, rounded curves.
                                    cubicInterpolationMode: 'default', // Set to 'default' for a solid and smooth line. 
                                    pointRadius: 0.2, // Set pointRadius to 0 to hide data point dots
                                    showLine: true, // Show the connecting line
                                    hidden: !dataset1Visible, // Initially visible or hidden based on flag	
                                };
                            } else {
                                console.log("value.length = ", value.length);
                                dataset1 = {
                                    label: firstCwmsTsId,
                                    data: date_time.map((element, index) => ({ x: new Date(element), y: value[index] })),
                                    yAxisID: 'y',
                                    borderColor: 'rgba(255, 0, 0, 1)', // red
                                    backgroundColor: 'rgba(255, 0, 0, 1)', // red
                                    borderWidth: 2,
                                    fill: false,
                                    tension: 1.0,
                                    cubicInterpolationMode: 'default',
                                    pointRadius: 0.2, 
                                    showLine: true,
                                    hidden: !dataset1Visible,
                                };
                            }


                        // lineChart
                            var lineChart = new Chart(ctx, {
                                type: 'line',
                                data: {
                                    labels: date_time, // Your X-axis labels
                                    datasets: [
                                        dataset1,
                                    ].filter(Boolean), // Filter out null datasets,
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
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
                                                    // Get the index of the current grid line
                                                    const index = context.index;
                                                    console.log("context.index = ", index);

                                                    // Get the tick values and their corresponding indices
                                                    const ticks = context.chart.scales.x.ticks;
                                                    console.log("ticks = ", ticks);

                                                    const tickIndex = context.index;
                                                    console.log("tickIndex = ", tickIndex);

                                                    // Check if ticks is defined and tickIndex is a valid index
                                                    if (ticks && tickIndex >= 0 && tickIndex < ticks.length) {
                                                        //console.log("tickIndex defined ticks array.");
                                                        const tick = ticks[tickIndex];

                                                        // Check if tick is defined before accessing its properties
                                                        if (tick) {
                                                            const tickValue = tick.value;
                                                            const tickLabel = tick.label;
                                                            
                                                            // Now you have access to the tick date-time, value, and label
                                                            console.log("Tick Date-Time:", new Date(tickValue));
                                                            console.log("Tick Value:", tickValue);
                                                            console.log("Tick Label:", tickLabel);
                                                            console.log("Tick Label for 18:00:", tickLabel);

                                                            // Define a regular expression pattern to match the format
                                                            const datePattern = /^[A-Z][a-z]{2} \d{1,2}, \d{4}$/;
                                                            const dateString = tickLabel;
                                                            console.log("dateString = ", dateString);

                                                            if (tickLabel == "06:00" || tickLabel == "12:00" || tickLabel == "18:00") {
                                                                //console.log("Date string matches 06:00, 12:00, and 18:00. Therefore, return 1");
                                                                return 1;
                                                            } else {
                                                                //console.log("Date string does match the format. Therefore, return 2");
                                                                return 3;
                                                            }
                                                        } else {
                                                            console.log("Tick is undefined.");
                                                            // You may want to return a default line width or handle the error here.
                                                        }
                                                    } else {
                                                        console.log("Invalid tickIndex or undefined ticks array.");
                                                        // You may want to return a default line width or handle the error here.
                                                    }
                                                },
                                                color: (context) => {
                                                    // Set the grid line color for thicker lines
                                                    return 'rgba(150, 150, 150, 0.8)'; //'rgba(192, 192, 192, 0.8)';
                                                },
                                            },
                                            ticks: {
                                                callback: function (value, index, values) {
                                                    // Create a new Date object from the timestamp value
                                                    const date = new Date(value);
                                                    //console.log("date = ", date);

                                                    if (isDaylightSavingTime(date)) {
                                                        console.log("isDaylightSavingTime = true");
                                                    } else {
                                                        console.log("isDaylightSavingTime = false");
                                                    }

                                                    // Extract utc hours from the timestamp
                                                    let current_hour_utc = date.getUTCHours();
                                                    console.log("current_hour_utc = ", current_hour_utc);

                                                    const DaylightSavingTime = (isDaylightSavingTime(date) ? 5 : 6);
                                                    console.log("DaylightSavingTime = ", DaylightSavingTime);

                                                    // take into account when utc is 24/0 subtract daylightsaving to get negative cst hours for the ticks
                                                    if (current_hour_utc === 0) {
                                                        console.log("current_hour_utc = 0");
                                                        current_hour_utc = 24;
                                                        console.log("current_hour_utc override =", current_hour_utc);
                                                    }

                                                    const current_hour_cst = current_hour_utc - DaylightSavingTime;
                                                    console.log("current_hour_cst = ", current_hour_cst);
                                                    
                                                    // Check if the tick should be displayed (6, 12, 18, and 0 hours)
                                                    if (current_hour_cst === 6 || current_hour_cst === 12 || current_hour_cst === 18) {
                                                        return new Intl.DateTimeFormat('en-US', {
                                                        hour: 'numeric',
                                                        minute: 'numeric',
                                                        hour12: false,
                                                        timeZone: 'America/Chicago' // Set the time zone to CST
                                                        }).format(new Date(value));
                                                    } else if (current_hour_cst === 0) {
                                                        return new Intl.DateTimeFormat('en-US', {
                                                        year: 'numeric', // Format the year as a 4-digit number (e.g., "2023")
                                                        month: 'short', // Format the month as the full name (e.g., "July") ("short" e.g., Aug)
                                                        day: 'numeric', // Format the day as a numeric value (e.g., "26")
                                                        hour12: false,
                                                        timeZone: 'America/Chicago', // Set the time zone to CST
                                                        }).format(new Date(value));
                                                    }
                                                },
                                                maxRotation: 90, // Set maxRotation to 0 for vertical labels
                                                minRotation: 90, // Set minRotation to 0 for vertical labels
                                                //borderWidth: 5, // Adjust the value to make the X-axis line thicker
                                            },
                                            title: {
                                                display: true,
                                                text: 'Date Time', // Add your Y-axis title here
                                                fontSize: 16, // Adjust the font size if needed
                                                fontColor: 'black', // Adjust the font color if needed
                                                padding: {
                                                    //top: 20, // Add spacing above the title
                                                    //bottom: 20, // Add spacing below the title
                                                },
                                            }
                                        },
                                        y: {
                                            suggestedMin: roundedMinValueAxis_y1, // Set a suggested minimum value
                                            suggestedMax: roundedMaxValueAxis_y1, // Set a suggested maximum value
                                            type: 'linear', // Assuming Y-axis is linear
                                            position: 'left', // Position on the left side
                                            title: {
                                                display: true,
                                                text: firstParameterId + ' ' + '(' + firstValueUnit + ')', // Add your Y-axis title here
                                                fontSize: 16, // Adjust the font size if needed
                                                fontColor: 'black', // Adjust the font color if needed
                                            },
                                            padding: {
                                                //top: 10,      // Padding at the top of the y-axis
                                                //bottom: 10,    // Padding at the bottom of the y-axis
                                            },
                                        },
                                    },
                                    plugins: {
                                        tooltip: {
                                            callbacks: {
                                                label: function(context) {
                                                    var value = context.parsed.y;
                                                    var datasetLabel = context.dataset.label; // Get the label of the dataset
                                                    var isY2Axis = context.dataset.yAxisID === 'y2'; // Check if the data point belongs to Y2-axis
                                                    if (firstParameterId == "Stage" || firstParameterId == "Conc-DO" || firstParameterId == "Elev") {
                                                        return value.toFixed(2) + " " + firstValueUnit + " " + datasetLabel + " (Y1-axis)";
                                                    } else if (firstParameterId == "Flow") {
                                                        if (value.toFixed(2) < 1000) {
                                                            return value.toFixed(0) + " " + firstValueUnit + " " + datasetLabel + " (Y1-axis)";
                                                        } else {
                                                            return (Math.floor(value / 10) * 10) + " " + firstValueUnit + " " + datasetLabel + " (Y1-axis)";
                                                        }
                                                    } else {
                                                        return value.toFixed(2) + " " + datasetLabel + " (Y1-axis)";
                                                    }
                                                }
                                            }
                                        },
                                        title: {
                                        display: true,
                                        text: js_start_day + ' Day ' + firstParameterId,
                                        position: 'top',
                                        font: {
                                            size: 16,
                                            weight: 'bold'
                                            }
                                        }
                                    },
                                    legend: {
                                        display: true,
                                        position: 'bottom', // This places the legend at the bottom
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

                        // After the chart has finished rendering, save it as PNG
                        lineChart.render(function() {
                            // Log to indicate the function is being executed
                            console.log("Rendering chart...");

                            // Get the canvas element
                            var canvas = document.getElementById('lineChart');

                            // Check if canvas element is retrieved successfully
                            if (!canvas) {
                                console.error("Canvas element not found!");
                                return;
                            }

                            // Convert canvas to Blob object
                            canvas.toBlob(function(blob) {
                                // Check if Blob object is created successfully
                                if (!blob) {
                                    console.error("Failed to create Blob object!");
                                    return;
                                }

                                // Log Blob object details
                                console.log("Blob object created:", blob);

                                // Create a temporary anchor element
                                var downloadLink = document.getElementById('downloadLink');

                                // Check if downloadLink element is retrieved successfully
                                if (!downloadLink) {
                                    console.error("Download link element not found!");
                                    return;
                                }

                                // Set the href attribute to the Blob object
                                downloadLink.href = URL.createObjectURL(blob);

                                // Log the created URL
                                console.log("URL created:", downloadLink.href);

                                // Set the download attribute with desired filename
                                downloadLink.download = '/wm/mvs/wm_web/var/apache2/2.4/htdocs/web_apps/plot_macro/public/images/chart_time_series.png';

                                // Log the filename
                                console.log("Filename set:", downloadLink.download);

                                // Trigger a click event to download the file
                                downloadLink.click();

                                // Log to indicate the download link is clicked
                                console.log("Download link clicked.");
                            });
                        });

                        </script>

                        <!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
                            </div>
                        </div>
                    </div>
                </div>
                <button id="returnTop" title="Return to Top of Page">Top</button>
            </div>
        </div>
        <footer id="footer">
            <!--Footer content populated here by script tag at end of body -->
        </footer>
        <script src="../../../js/libraries/jQuery-3.3.6.min.js"></script>
        <script defer>
            // When the document has loaded pull in the page header and footer skins
            $(document).ready(function () {
                // Change the v= to a different number to force clearing the cached version on the client browser
                $('#header').load('../../../templates/INTERNAL.header.php');
                $('#sidebar').load('../../../templates/INTERNAL.sidebar.php');
                $('#footer').load('../../../templates/INTERNAL.footer.php');
            })
        </script>
    </body>

</html>
<?php db_disconnect($db); ?>