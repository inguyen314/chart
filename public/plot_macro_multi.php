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

// GET REQUEST 
$type = $_GET['type'] ?? '1';
$basin = $_GET['basin'] ?? '1';
?>

<!-- Include Moment.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<!-- Include the Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Include the Moment.js adapter for Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>

<!-- Include the CSS Style -->
<link href="stylesheets/style.css" rel="stylesheet" type="text/css" media="all" />

<!-- Include the JQuery -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<?php
function get_table_data_local($db, $cwms_ts_id, $start_day, $end_day) {
	$stmnt_query = null;
	$data = [];
	
	try {		
		$sql = "select cwms_ts_id
					,cwms_util.change_timezone(tsv.date_time, 'UTC', 'CST6CDT' ) as date_time
					,cwms_util.split_text('".$cwms_ts_id."' ,1,'.') as location_id
					,cwms_util.split_text('".$cwms_ts_id."' ,2,'.') as parameter_id
					,cwms_util.split_text('".$cwms_ts_id."' ,6,'.') as version_id
					,value
					,unit_id
					,quality_code
				from cwms_v_tsv_dqu_30d  tsv
					where 
						tsv.cwms_ts_id = '".$cwms_ts_id."'  
						and date_time  >= cast(cast(current_date as timestamp) at time zone 'UTC' as date) - interval '".$start_day."' DAY
						and date_time  <= cast(cast(current_date as timestamp) at time zone 'UTC' as date) + interval '".$end_day."' DAY
						and (tsv.unit_id = 'ppm' or tsv.unit_id = 'F' or tsv.unit_id = CASE WHEN cwms_util.split_text(tsv.cwms_ts_id,2,'.') IN ('Stage','Elev','Opening') THEN 'ft' WHEN cwms_util.split_text(tsv.cwms_ts_id,2,'.') IN ('Precip','Depth') THEN 'in' END or tsv.unit_id = 'cfs' or tsv.unit_id = 'umho/cm' or tsv.unit_id = 'volt' or tsv.unit_id = 'ac-ft')
						and tsv.office_id = 'MVS' 
						and tsv.aliased_item is null
					order by date_time desc";
		
		$stmnt_query = oci_parse($db, $sql);
		$status = oci_execute($stmnt_query);

		while (($row = oci_fetch_array($stmnt_query, OCI_ASSOC+OCI_RETURN_NULLS)) !== false) {
			$obj = (object) [
				"cwms_ts_id" => $row['CWMS_TS_ID'],
				"date_time" => $row['DATE_TIME'],
				"location_id" => $row['LOCATION_ID'],
				"parameter_id" => $row['PARAMETER_ID'],
				"version_id" => $row['VERSION_ID'],
				"value" => $row['VALUE'],
				"unit_id" => $row['UNIT_ID'],
				"quality_code" => $row['QUALITY_CODE']
			];
			array_push($data, $obj);
		}
	}
	catch (Exception $e) {
		$e = oci_error($db);  
		trigger_error(htmlentities($e['message']), E_USER_ERROR);
        
		return null;
	}
	finally {
		oci_free_statement($stmnt_query); 
	}
	return $data;
}

// Your JSON file path
if ($type == "ld") {
    $json_file_path = 'csv/ld.json';
} else {
    if ($basin == "Big Muddy") {
        $json_file_path = 'csv/big_muddy.json';
    } else if ($basin == "Cuivre") {
        $json_file_path = 'csv/cuivre.json';
    } else if ($basin == "Illinois") {
        $json_file_path = 'csv/illinois.json';
    } else if ($basin == "Kaskaskia") {
        $json_file_path = 'csv/kaskaskia.json';
    } else if ($basin == "Meramec") {
        $json_file_path = 'csv/meramec.json';
    } else if ($basin == "Mississippi") {
        $json_file_path = 'csv/mississippi.json';
    } else if ($basin == "Missouri") {
        $json_file_path = 'csv/missouri.json';
    } else if ($basin == "Ohio") {
        $json_file_path = 'csv/ohio.json';
    } else if ($basin == "Salt") {
        $json_file_path = 'csv/salt.json';
    } else if ($basin == "St Francis") {
        $json_file_path = 'csv/st_francis.json';
    } else {
        $json_file_path = 'csv/ld.json';
    }
}

// Read the JSON file and decode it
$json_data = file_get_contents($json_file_path);
$data = json_decode($json_data, true);

// Check if decoding was successful
if ($data !== null) {
    // Initialize an array to store time series data for each location
    $timeSeriesData = array();

    // Loop through the JSON data
    foreach ($data as $item) {
        $projectId = $item['project_id'];

        // Process time series data for each CWMS entry
        foreach ($item as $key => $cwmsEntry) {
            if (strpos($key, 'cwms_') === 0) {
                $locationId = $cwmsEntry['location_id'];
                $tsid = $cwmsEntry['tsid'];
                $startDay = $cwmsEntry['start_day'];
                $endDay = $cwmsEntry['end_day'];

                $tableData = get_table_data_local($db, $tsid, $startDay, $endDay);
                $timeSeriesData["$projectId - $locationId"][] = array(
                    'table_data' => $tableData
                );
            }
        }
        
    }

    // Output the processed time series data as JSON
    echo '<script>';
    echo 'var timeSeriesData = ' . json_encode($timeSeriesData) . ';';
    echo '</script>';
} else {
    echo "Error decoding the JSON file.";
}
?>

<script>
console.log("timeSeriesData: ", timeSeriesData); // Verify the data is available in the browser console

// Function: check Daylight Saving Time
function isDaylightSavingTime(date) {
        const january = new Date(date.getFullYear(), 0, 1);
        const july = new Date(date.getFullYear(), 6, 1);
        const stdTimezoneOffset = Math.max(january.getTimezoneOffset(), july.getTimezoneOffset());
        return date.getTimezoneOffset() < stdTimezoneOffset;
    }


// Check if timeSeriesData is defined and not empty
if (typeof timeSeriesData !== 'undefined' && Object.keys(timeSeriesData).length > 0) {
    // Create a container for the charts
    document.write('<div style="display: flex; flex-wrap: wrap;">');

    // Track whether each project's h1 tag has been displayed
    const displayedProjects = {};

    

    // Create a chart for each location
    Object.keys(timeSeriesData).forEach(projectLocation => {
        const [projectId, location] = projectLocation.split(' - ');
        const locationData = timeSeriesData[projectLocation];
        console.log("locationData: ", locationData);

        // Extract parameter_id and unit_id from the first entry in locationData
        const firstEntry = locationData[0].table_data[0];
        const parameterId = firstEntry.parameter_id;
        const unitId = firstEntry.unit_id;

        // Calculate min and max values for the location
        let minValue = Number.MAX_VALUE;
        let maxValue = Number.MIN_VALUE;

        // Iterate through the locationData to find min and max values
        locationData.forEach(entry => {
            entry.table_data.forEach(tableItem => {
                const value = parseFloat(tableItem.value);
                if (!isNaN(value)) {
                    minValue = Math.min(minValue, value);
                    maxValue = Math.max(maxValue, value);
                }
            });
        });

        // Initialize variables for min and max values
        let overallMinValue = Number.MAX_VALUE;
        let overallMaxValue = Number.MIN_VALUE;

        // Update overall min and max values
        overallMinValue = Math.min(overallMinValue, minValue);
        overallMaxValue = Math.max(overallMaxValue, maxValue);

        console.log("overallMinValue: ", overallMinValue);
        console.log("overallMaxValue: ", overallMaxValue);

        let roundedMinValue;
        let roundedMaxValue;

        // adjust min and max value
        if (overallMinValue <= 0) { 
            var adjustedMinValue = overallMinValue -1;
            console.log("less than 0");
        } else if (overallMinValue <= 10) {
            var adjustedMinValue = overallMinValue - 1;
            console.log("less than 10");
        } else if (overallMinValue>200) {
            console.log("greater than 200");
            var adjustedMinValue = overallMinValue -1;
        } else {
            var adjustedMinValue = overallMinValue - (overallMinValue*0.1);
            console.log("minus 10%");
        }
        console.log("adjustedMinValue = ", adjustedMinValue);

        if (overallMaxValue<=0) {
            console.log("less than 0");
            var adjustedMaxValue = overallMaxValue + 1;
        } else if (overallMaxValue <= 10) {
            console.log("less than 10");
            var adjustedMaxValue = overallMaxValue + 1;
        } else if (overallMaxValue > 200) {
            console.log("greater than 200");
            var adjustedMaxValue = overallMaxValue + 1;
        } else {
            var adjustedMaxValue = overallMaxValue + (overallMaxValue*0.1);
            console.log("plus 10%");
        }
        console.log("adjustedMaxValue = ", adjustedMaxValue);


        // SAME PARAMETER: ROUNDED MIN AND MAX Y-AXIS
        roundedMinValue = Math.round(adjustedMinValue);
        console.log("roundedMinValue = ", roundedMinValue);
        roundedMaxValue = Math.round(adjustedMaxValue);
        console.log("roundedMaxValue = ", roundedMaxValue);


        // Create a container for each chart with dynamic height
        document.write('<div style="width: 50%;' +
            (!displayedProjects[projectId] ? '' : 'margin-top: 70px;') + // Add margin if h1 is displayed
            '">');

        // Check if this is the first location for the project
        if (!displayedProjects[projectId] && locationData.length > 0 && locationData[0].table_data.length > 0) {
            document.write(`<h1>${projectId}</h1>`);
            displayedProjects[projectId] = true; // Mark the project as displayed
        }

        // Create a canvas for each location
        const canvasId = `chart-${projectId}-${location}`;
        document.write(`<canvas id="${canvasId}" width="400" height="200"></canvas>`);

        // Get the context of the canvas
        const ctx = document.getElementById(canvasId).getContext('2d');

        // Create a chart for the location
        new Chart(ctx, {
            type: 'line',
            data: {
                datasets: [{
                    label: `${location}`,
                    borderColor: 'rgba(255, 0, 0, 1)', // red
                    backgroundColor: 'rgba(255, 0, 0, 1)', // red
                    data: locationData[0].table_data.map(entry => ({
                        x: moment(entry.date_time, "MM-DD-YYYY HH:mm").isValid() ? moment(entry.date_time, "MM-DD-YYYY HH:mm").toDate() : null,
                        y: parseFloat(entry.value)
                    })),
                    fill: false
                }]
            },
            options: {
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'hour',
                            stepSize: 6,
                            tooltipFormat: 'MMM D, YYYY HH:mm',
                            displayFormats: {
                                hour: 'HH:mm',
                                minute: 'HH:mm',
                            }
                        },
                        title: {
                            display: true,
                            text: 'Date Time', // Add your Y-axis title here
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
                    },
                    y: {
                        suggestedMin: roundedMinValue, // Set a suggested minimum value
                        suggestedMax: roundedMaxValue, // Set a suggested maximum value
                        title: {
                            display: true,
                            text: `${parameterId} (${unitId})`,
                        },
                    }
                }
            }
        });

        // Close the chart container div
        document.write('</div>');
    });

    // Close the overall container div
    document.write('</div>');
} else {
    console.error("timeSeriesData is not defined or empty.");
}
</script>
<!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
<?php db_disconnect($db); ?>

<!-- version 4. adjusted y axis min and max -->
<!-- version 5. adjust date time x axis -->