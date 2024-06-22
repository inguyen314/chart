<?php
//5. add missing data link. set maintainAspectRatio: true.
//6. make plot full screen if no basin. change url ?? to null. remove sidebar red bar for full sized plot. 
//7. add "hidden" in Update Plot dropdown box. remove all "1" to null in get request url. add tofixed to "in" in table table.

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
    $cwms_ts_id = $_GET['cwms_ts_id'] ?? null;
    $cwms_ts_id_2 = $_GET['cwms_ts_id_2'] ?? null;
    $cwms_ts_id_3 = $_GET['cwms_ts_id_3'] ?? null;
    $cwms_ts_id_4 = $_GET['cwms_ts_id_4'] ?? null;
    $cwms_ts_id_5 = $_GET['cwms_ts_id_5'] ?? null;
    $cwms_ts_id_6 = $_GET['cwms_ts_id_6'] ?? null;
    $cwms_ts_id_7 = $_GET['cwms_ts_id_7'] ?? null;
    $start_day = $_GET['start_day'] ?? null;
    $end_day = $_GET['end_day'] ?? null;
    $gage_data = $_GET['gage_data'] ?? null;
?>

<?php
$now =  date('Y-m-d H:i');
$time   = strtotime($now);
$current_date =  date('m-d-Y');
$current_date_time_format =  date('m-d-Y H:i');
$current_date_time   = date("Y-m-d H:i", $time);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Plot Macro - <?php if ($basin == null) { echo $cwms_ts_id; } else { echo $basin; }; ?></title>
        <meta name="Description" content="U.S. Army Corps of Engineers St. Louis District Home Page" />
        <link rel="stylesheet" href="../../../css/body.css" />
        <link rel="stylesheet" href="../../../css/breadcrumbs.css" />
        <link rel="stylesheet" href="../../../css/jumpMenu.css" />
        <script type="text/javascript" src="../../../js/main.js"></script>

        <!-- Additional CSS -->
        <link rel="stylesheet" href="../../../css/rebuild.css" />
        <link href="stylesheets/style.css" rel="stylesheet" type="text/css" media="all" />
        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <!-- Include Moment.js -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
        <!-- Include the Chart.js library -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <!-- Include the Moment.js adapter for Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment@1.0.0"></script>
    </head>
    <body>
        <div id="page-container">
            <header id="header">
                <!--Header content populated here by JavaScript Tag at end of body -->
            </header>
            <div class="page-wrap">
                <div class="container-fluid" style="margin-bottom:50px;">
                    <div id="breadcrumbs">
                    </div>
                    <?php
                    if ($basin == null) {
                        echo '<div class="page-content">
                            <div id="topPane" class="col-md backend-cp-collapsible">
                                <div class="box-usace">
                                    <h2 class="box-header-striped">
                                        <span class="titleLabel title">Plot Macro</span>
                                        <span class="rss"></span>
                                    </h2>
                                    <div class="box-content" style="background-color:white; margin:auto">
                                        <div class="content">
                                            <!-- Box Content Here -->
                                            <span>Last Modified:' . $current_date_time . '</span><br>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                        echo "<div id='chart'><canvas id='lineChart'></canvas></div>";
                    }
                    ?>
                    <div class="page-content">
                        <sidebar id="sidebar">
                            <!--Side bar content populated here by JavaScript Tag at end of body -->
                        </sidebar>
                        <div id="topPane" class="col-md backend-cp-collapsible">
                            <!--////////////////////////////////////////////////////////////////////////////////////////////////////////////////////-->
                            <!--APP START-->
                            <div class="box-usace">
                                <h2 class="box-header-striped">
                                    <span class="titleLabel title"><?php echo strstr($cwms_ts_id, '.', true); ?></span>
                                    <span class="rss"></span>
                                </h2>
                                <div class="box-content" style="background-color:white;margin:auto">
                                    <div class="content">
                                    <!-- Plot Chart -->
                                        <?php
                                        if ($basin !== null) {
                                            echo "<span>Last Modified:" . $current_date_time . "</span><br><br>";
                                            echo "<div id='chart'><canvas id='lineChart'></canvas></div>";
                                        }
                                        ?>
                                    <!-- Location Level -->
                                        <div id="metaData" style="width: 100%; height: auto;border: 1px solid #ccc; margin-top: 15px; margin-bottom: 15px; padding: 10px;"></div>
                                    <!-- Update Plot -->
                                        <?php
                                            echo '<div id="formDataUpdatePlot" style="width: 100%; border: 1px solid #ccc; margin-top: 15px; margin-bottom: 15px; padding: 10px;">';
                                            echo '    <label for="selectedBasinUpdatePlot">Selected Basin:</label>';
                                            echo '    <select id="selectedBasinUpdatePlot" name="basin">';
                                            echo '        <!-- Options will be populated dynamically using JavaScript -->';
                                            echo '    </select>';
                                            echo '    <br>';
                                            echo '    <br>';
                                            echo '    <label for="selectedTimeSeries01">Defaulted Time Series 1:</label>';
                                            echo '    <select id="selectedTimeSeries01" name="cwms_ts_id_01">';
                                            echo '        <!-- Options will be populated dynamically using JavaScript -->';
                                            echo '    </select>';
                                            echo '    <br>';
                                            echo '    <br>';

                                            if ($cwms_ts_id_2 != null) {
                                            echo '';
                                            echo '    <label for="selectedTimeSeries02">Defaulted Time Series 2:</label>';
                                            echo '    <select id="selectedTimeSeries02" name="cwms_ts_id_02">';
                                            echo '        <!-- Options will be populated dynamically using JavaScript -->';
                                            echo '    </select>';
                                            echo '    <br>';
                                            echo '    <br>';
                                            }
                                
                                            if ($cwms_ts_id_3 != null) {
                                            echo '    <label for="selectedTimeSeries03">Defaulted Time Series 3:</label>';
                                            echo '    <select id="selectedTimeSeries03" name="cwms_ts_id_03">';
                                            echo '        <!-- Options will be populated dynamically using JavaScript -->';
                                            echo '    </select>';
                                            echo '    <br>';
                                            echo '    <br>';
                                            }
                                            
                                            if ($cwms_ts_id_4 != null) {
                                            echo '    <label for="selectedTimeSeries04">Defaulted Time Series 4:</label>';
                                            echo '    <select id="selectedTimeSeries04" name="cwms_ts_id_04">';
                                            echo '        <!-- Options will be populated dynamically using JavaScript -->';
                                            echo '    </select>';
                                            echo '    <br>';
                                            echo '    <br>';
                                            }
                                            
                                            if ($cwms_ts_id_5 != null) {
                                            echo '    <label for="selectedTimeSeries05">Defaulted Time Series 5:</label>';
                                            echo '    <select id="selectedTimeSeries05" name="cwms_ts_id_05">';
                                            echo '        <!-- Options will be populated dynamically using JavaScript -->';
                                            echo '    </select>';
                                            echo '    <br>';
                                            echo '    <br>';
                                            }
                                            
                                            if ($cwms_ts_id_6 != null) {
                                            echo '    <label for="selectedTimeSeries06">Defaulted Time Series 6:</label>';
                                            echo '    <select id="selectedTimeSeries06" name="cwms_ts_id_06">';
                                            echo '        <!-- Options will be populated dynamically using JavaScript -->';
                                            echo '    </select>';
                                            echo '    <br>';
                                            echo '    <br>';
                                            }
                                            
                                            if ($cwms_ts_id_7 != null) {
                                            echo '    <label for="selectedTimeSeries07">Defaulted Time Series 7:</label>';
                                            echo '    <select id="selectedTimeSeries07" name="cwms_ts_id_07">';
                                            echo '        <!-- Options will be populated dynamically using JavaScript -->';
                                            echo '    </select>';
                                            echo '    <br>';
                                            echo '    <br>';
                                            }
                                            
                                            echo '    <label for="selectStartDay">Select Start Day:</label>';
                                            echo '    <select id="selectStartDay" name="start_day">';
                                            echo '        <option value="0">0 day</option>';
                                            echo '        <option value="4" selected>4 days</option> <!-- Set this option as selected -->';
                                            echo '        <option value="7">7 days</option>';
                                            echo '        <option value="14">14 days</option>';
                                            echo '        <option value="30">30 days</option>';
                                            echo '        <option value="90">90 days</option>';
                                            echo '    </select>';
                                            echo '    <br>';
                                            echo '    <br>';
                                            
                                            echo '    <label for="selectEndDay">Select End Day:</label>';
                                            echo '    <select id="selectEndDay" name="end_day">';
                                            echo '        <option value="0" selected>0 day</option>';
                                            echo '        <option value="4">4 days</option>';
                                            echo '        <option value="7">7 days</option>';
                                            echo '        <option value="14">14 days</option>';
                                            echo '        <option value="30">30 days</option>';
                                            echo '        <option value="90">90 days</option>';
                                            echo '    </select>';
                                            echo '    <br>';
                                            echo '    <br>';
                                            
                                            echo '    <!-- You can use JavaScript to get the selected value -->';
                                            echo '    <button onclick="getUpdatedPlotValues()" class="modern-button-update">Update Plot</button>';
                                            echo '</div>';
                                        ?>
                                    <!-- Switch Basin -->
                                        <?php
                                            if ($basin !== null) {
                                                echo '<div id="formDataSwitchBasin" style="width: 100%; border: 1px solid #ccc; margin-top: 15px; margin-bottom: 15px; padding: 10px;">';
                                                echo '    <label for="selectSwitchBasinBox">Select a Basin:</label>';
                                                echo '    <select id="selectSwitchBasinBox" name="basin" onchange="populateSwitchBasinSecondDropdown()">';
                                                echo '    </select>';
                                                echo '    <br>';
                                                echo '    <br>';
                                                echo '    <label for="selectSwitchBasinTimeSeriesBox">Defaulted Time Series:</label>';
                                                echo '    <select id="selectSwitchBasinTimeSeriesBox" name="cwmsTsId">';
                                                echo '    </select>';
                                                echo '    <br>';
                                                echo '    <br>';
                                                echo '    <button onclick="getUpdatedSwitchBasinValues()" class="modern-button-switch">Switch Basin</button>';
                                                echo '</div>';
                                            }
                                        ?>
                                    <!-- Switch Parameter -->
                                        <div id="changePlotParameter" style="width: 100%; border: 1px solid #ccc; margin-top: 15px; margin-bottom: 15px; padding: 10px;"></div>
                                    <!-- Quality Codes -->
                                        <div id="qualityCodesTable" style="width: 100%; border: 1px solid #ccc; margin-top: 15px; margin-bottom: 15px; padding: 10px;"></div>
                                    <!-- MetaData -->
                                        <div style="width: 100%; height: auto;border: 1px solid #ccc; margin-top: 15px; margin-bottom: 15px; padding: 10px;">
                                            <button onclick="getMetaData('<?php echo $cwms_ts_id; ?>')" class="modern-button-meta">Load MetaData</button>
                                            <div id="result" style="width: 100%; height: auto;border: 1px solid #fff;"></div>
                                        </div>
                                    <!-- Datman Data -->
                                        <div style="width: 100%; height: auto;border: 1px solid #ccc; margin-top: 15px; margin-bottom: 15px; padding: 10px;">
                                            <button onclick="getDataDatman('<?php echo $cwms_ts_id; ?>')" class="modern-button-datman">Load Datman MetaData</button>
                                            <div id="resultDatman" style="width: 100%; height: auto;border: 1px solid #fff;"></div>
                                        </div>
                                    <!-- Table Data -->
                                        <div style="width: 100%; border: 1px solid #ccc; margin-top: 10px; padding: 10px;">
                                            <div id="buttonContainer"></div>
                                            <div id="dataTable"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                                
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


                            // GET TIME SERIES 2
                                $table_data_2 = get_table_data($db, $cwms_ts_id_2, $start_day, $end_day);
                                //print_r($table_data_2);

                                $json_data_2 = json_encode($table_data_2); // JSON string
                                //print_r($json_data_2);

                                $data_2 = json_decode($json_data_2, true); // Associative array
                                //print_r($data_2);

                                $cwms_ts_id_2 = array_column($data_2, 'cwms_ts_id');
                                $location_id_2 = array_column($data_2, 'location_id');
                                $parameter_id_2 = array_column($data_2, 'parameter_id');
                                $date_time_2 = array_column($data_2, 'date_time');
                                $value_2 = array_column($data_2, 'value');
                                $unit_2 = array_column($data_2, 'unit_id');
                                $quality_code_2 = array_column($data_2, 'quality_code');

                                if (array_key_exists(0, $cwms_ts_id_2)) {
                                    // The key 0 exists in $yourArray
                                    $cwms_ts_id_2_first_element = $cwms_ts_id_2[0];
                                    //print_r($cwms_ts_id_2_first_element);
                                
                                    $location_id_2_first_element = $location_id_2[0];
                                    //print_r($location_id_2_first_element);
                                } else {
                                    // The key 0 does not exist in $yourArray
                                    $cwms_ts_id_2_first_element = null;
                                    $location_id_2_first_element = null;
                                }


                            // GET TIME SERIES 3
                                $table_data_3 = get_table_data($db, $cwms_ts_id_3, $start_day, $end_day);
                                //print_r($table_data_3);

                                $json_data_3 = json_encode($table_data_3); // JSON string
                                //print_r($json_data_3);

                                $data_3 = json_decode($json_data_3, true); // Associative array
                                //print_r($data_3);

                                $cwms_ts_id_3 = array_column($data_3, 'cwms_ts_id');
                                $location_id_3 = array_column($data_3, 'location_id');
                                $parameter_id_3 = array_column($data_3, 'parameter_id');
                                $date_time_3 = array_column($data_3, 'date_time');
                                $value_3 = array_column($data_3, 'value');
                                $unit_3 = array_column($data_3, 'unit_id');
                                $quality_code_3 = array_column($data_3, 'quality_code');

                                if (array_key_exists(0, $cwms_ts_id_3)) {
                                    // The key 0 exists in $yourArray
                                    $cwms_ts_id_3_first_element = $cwms_ts_id_3[0];
                                    //print_r($cwms_ts_id_3_first_element);
                                
                                    $location_id_3_first_element = $location_id_3[0];
                                    //print_r($location_id_3_first_element);
                                } else {
                                    // The key 0 does not exist in $yourArray
                                    $cwms_ts_id_3_first_element = null;
                                    $location_id_3_first_element = null;
                                }

                            // GET TIME SERIES 4
                                $table_data_4 = get_table_data($db, $cwms_ts_id_4, $start_day, $end_day);
                                //print_r($table_data_4);

                                $json_data_4 = json_encode($table_data_4); // JSON string
                                //print_r($json_data_4);

                                $data_4 = json_decode($json_data_4, true); // Associative array
                                //print_r($data_4);

                                $cwms_ts_id_4 = array_column($data_4, 'cwms_ts_id');
                                $location_id_4 = array_column($data_4, 'location_id');
                                $parameter_id_4 = array_column($data_4, 'parameter_id');
                                $date_time_4 = array_column($data_4, 'date_time');
                                $value_4 = array_column($data_4, 'value');
                                $unit_4 = array_column($data_4, 'unit_id');
                                $quality_code_4 = array_column($data_4, 'quality_code');

                                if (array_key_exists(0, $cwms_ts_id_4)) {
                                    // The key 0 exists in $yourArray
                                    $cwms_ts_id_4_first_element = $cwms_ts_id_4[0];
                                    //print_r($cwms_ts_id_4_first_element);
                                
                                    $location_id_4_first_element = $location_id_4[0];
                                    //print_r($location_id_4_first_element);
                                } else {
                                    // The key 0 does not exist in $yourArray
                                    $cwms_ts_id_4_first_element = null;
                                    $location_id_4_first_element = null;
                                }

                            // GET TIME SERIES 5
                                $table_data_5 = get_table_data($db, $cwms_ts_id_5, $start_day, $end_day);
                                //print_r($table_data_5);

                                $json_data_5 = json_encode($table_data_5); // JSON string
                                //print_r($json_data_5);

                                $data_5 = json_decode($json_data_5, true); // Associative array
                                //print_r($data_5);

                                $cwms_ts_id_5 = array_column($data_5, 'cwms_ts_id');
                                $location_id_5 = array_column($data_5, 'location_id');
                                $parameter_id_5 = array_column($data_5, 'parameter_id');
                                $date_time_5 = array_column($data_5, 'date_time');
                                $value_5 = array_column($data_5, 'value');
                                $unit_5 = array_column($data_5, 'unit_id');
                                $quality_code_5 = array_column($data_5, 'quality_code');

                                if (array_key_exists(0, $cwms_ts_id_5)) {
                                    // The key 0 exists in $yourArray
                                    $cwms_ts_id_5_first_element = $cwms_ts_id_5[0];
                                    //print_r($cwms_ts_id_5_first_element);
                                
                                    $location_id_5_first_element = $location_id_5[0];
                                    //print_r($location_id_5_first_element);
                                } else {
                                    // The key 0 does not exist in $yourArray
                                    $cwms_ts_id_5_first_element = null;
                                    $location_id_5_first_element = null;
                                }

                            // GET TIME SERIES 6
                                $table_data_6 = get_table_data($db, $cwms_ts_id_6, $start_day, $end_day);
                                //print_r($table_data_6);

                                $json_data_6 = json_encode($table_data_6); // JSON string
                                //print_r($json_data_6);

                                $data_6 = json_decode($json_data_6, true); // Associative array
                                //print_r($data_6);

                                $cwms_ts_id_6 = array_column($data_6, 'cwms_ts_id');
                                $location_id_6 = array_column($data_6, 'location_id');
                                $parameter_id_6 = array_column($data_6, 'parameter_id');
                                $date_time_6 = array_column($data_6, 'date_time');
                                $value_6 = array_column($data_6, 'value');
                                $unit_6 = array_column($data_6, 'unit_id');
                                $quality_code_6 = array_column($data_6, 'quality_code');

                                if (array_key_exists(0, $cwms_ts_id_6)) {
                                    // The key 0 exists in $yourArray
                                    $cwms_ts_id_6_first_element = $cwms_ts_id_6[0];
                                    //print_r($cwms_ts_id_6_first_element);
                                
                                    $location_id_6_first_element = $location_id_6[0];
                                    //print_r($location_id_6_first_element);
                                } else {
                                    // The key 0 does not exist in $yourArray
                                    $cwms_ts_id_6_first_element = null;
                                    $location_id_6_first_element = null;
                                }

                            // GET TIME SERIES 7
                                $table_data_7 = get_table_data($db, $cwms_ts_id_7, $start_day, $end_day);
                                //print_r($table_data_7);

                                $json_data_7 = json_encode($table_data_7); // JSON string
                                //print_r($json_data_7);

                                $data_7 = json_decode($json_data_7, true); // Associative array
                                //print_r($data_7);

                                $cwms_ts_id_7 = array_column($data_7, 'cwms_ts_id');
                                $location_id_7 = array_column($data_7, 'location_id');
                                $parameter_id_7 = array_column($data_7, 'parameter_id');
                                $date_time_7 = array_column($data_7, 'date_time');
                                $value_7 = array_column($data_7, 'value');
                                $unit_7 = array_column($data_7, 'unit_id');
                                $quality_code_7 = array_column($data_7, 'quality_code');

                                if (array_key_exists(0, $cwms_ts_id_7)) {
                                    // The key 0 exists in $yourArray
                                    $cwms_ts_id_7_first_element = $cwms_ts_id_7[0];
                                    //print_r($cwms_ts_id_7_first_element);
                                
                                    $location_id_7_first_element = $location_id_7[0];
                                    //print_r($location_id_7_first_element);
                                } else {
                                    // The key 0 does not exist in $yourArray
                                    $cwms_ts_id_7_first_element = null;
                                    $location_id_7_first_element = null;
                                }

                            // GET FLOOD LEVEL
                                $specified_level_id = "Flood";
                                $table_data_8 = get_level($db, $location_id_first_element, $specified_level_id);
                                //print_r($table_data_8);
                                if ($table_data_8 !== null) {
                                    $location_level_id_flood = $table_data_8->location_level_id;
                                    $specified_level_id_flood = $table_data_8->specified_level_id;
                                    $constant_level_flood = $table_data_8->constant_level;
                                } else {
                                    $constant_level_flood = 909;
                                }

                                // Initialize an empty array for the constant level time series
                                $constant_level_time_series_flood = [];

                                // Loop through the date_time array and assign the constant value to each timestamp
                                foreach ($date_time as $timestamp) {
                                    $constant_level_time_series_flood[] = [
                                        'date_time' => $timestamp,
                                        'value' => $constant_level_flood,
                                    ];
                                }

                                // Now, $constant_level_time_series_flood contains the time series with a constant level value
                                //print_r($constant_level_time_series_flood);

                                // Initialize separate arrays for date_time and constant_value
                                $date_time_array_flood = [];
                                $constant_value_array_flood = [];

                                // Loop through the combined array to separate the values
                                foreach ($constant_level_time_series_flood as $item) {
                                    $date_time_array_flood[] = $item["date_time"];
                                    $constant_value_array_flood[] = $item["value"];
                                }

                                // Now, $date_time_array contains the date_time values, and $constant_value_array contains the constant_value values
                                //print_r($date_time_array_flood);
                                //print_r($constant_value_array_flood);
                            
                                
                                
                            // GET LWRP
                                $specified_level_id = "LWRP";
                                $table_data_9 = get_level($db, $location_id_first_element, $specified_level_id);
                                //print_r($table_data_9);

                                if ($table_data_9 !== null) {
                                    $location_level_id_lwrp = $table_data_9->location_level_id;
                                    $specified_level_id_lwrp = $table_data_9->specified_level_id;
                                    $constant_level_lwrp = $table_data_9->constant_level;
                                } else {
                                    $constant_level_lwrp = 909;
                                }


                                // Initialize an empty array for the constant level time series
                                $constant_level_time_series_lwrp = [];

                                // Loop through the date_time array and assign the constant value to each timestamp
                                foreach ($date_time as $timestamp) {
                                    $constant_level_time_series_lwrp[] = [
                                        'date_time' => $timestamp,
                                        'value' => $constant_level_lwrp,
                                    ];
                                }

                                // Now, $constant_level_time_series contains the time series with a constant level value
                                //print_r($constant_level_time_series_lwrp);

                                // Initialize separate arrays for date_time and constant_value
                                $date_time_array_lwrp = [];
                                $constant_value_array_lwrp = [];

                                // Loop through the combined array to separate the values
                                foreach ($constant_level_time_series_lwrp as $item) {
                                    $date_time_array_lwrp[] = $item["date_time"];
                                    $constant_value_array_lwrp[] = $item["value"];
                                }

                                // Now, $date_time_array contains the date_time values, and $constant_value_array contains the constant_value values
                                //print_r($date_time_array_lwrp);
                                //print_r($constant_value_array_lwrp);


                            // GET HINGE MAX
                                $specified_level_id = "Hinge Max";
                                $table_data_10 = get_level($db, $location_id_first_element, $specified_level_id);
                                //print_r($table_data_10);

                                if ($table_data_10 !== null) {
                                    $location_level_id_hinge_max = $table_data_10->location_level_id;
                                    $specified_level_id_hinge_max = $table_data_10->specified_level_id;
                                    $constant_level_hinge_max = $table_data_10->constant_level;
                                } else {
                                    $constant_level_hinge_max = 909;
                                }

                                // Initialize an empty array for the constant level time series
                                $constant_level_time_series_hinge_max = [];

                                // Loop through the date_time array and assign the constant value to each timestamp
                                foreach ($date_time as $timestamp) {
                                    $constant_level_time_series_hinge_max[] = [
                                        'date_time' => $timestamp,
                                        'value' => $constant_level_hinge_max,
                                    ];
                                }

                                // Now, $constant_level_time_series contains the time series with a constant level value
                                //print_r($constant_level_time_series_hinge_max);

                                // Initialize separate arrays for date_time and constant_value
                                $date_time_array_hinge_max = [];
                                $constant_value_array_hinge_max = [];

                                // Loop through the combined array to separate the values
                                foreach ($constant_level_time_series_hinge_max as $item) {
                                    $date_time_array_hinge_max[] = $item["date_time"];
                                    $constant_value_array_hinge_max[] = $item["value"];
                                }

                                // Now, $date_time_array contains the date_time values, and $constant_value_array contains the constant_value values
                                //print_r($date_time_array_hinge_max);
                                //print_r($constant_value_array_hinge_max);   


                            // GET HINGE MIN
                                $specified_level_id = "Hinge Min";
                                $table_data_11 = get_level($db, $location_id_first_element, $specified_level_id);
                                //print_r($table_data_10);

                                if ($table_data_11 !== null) {
                                    $location_level_id_hinge_min = $table_data_11->location_level_id;
                                    $specified_level_id_hinge_min = $table_data_11->specified_level_id;
                                    $constant_level_hinge_min = $table_data_11->constant_level;
                                } else {
                                    $constant_level_hinge_min = 909;
                                }

                                // Initialize an empty array for the constant level time series
                                $constant_level_time_series_hinge_min = [];

                                // Loop through the date_time array and assign the constant value to each timestamp
                                foreach ($date_time as $timestamp) {
                                    $constant_level_time_series_hinge_min[] = [
                                        'date_time' => $timestamp,
                                        'value' => $constant_level_hinge_min,
                                    ];
                                }

                                // Now, $constant_level_time_series contains the time series with a constant level value
                                //print_r($constant_level_time_series_hinge_min);

                                // Initialize separate arrays for date_time and constant_value
                                $date_time_array_hinge_min = [];
                                $constant_value_array_hinge_min = [];

                                // Loop through the combined array to separate the values
                                foreach ($constant_level_time_series_hinge_min as $item) {
                                    $date_time_array_hinge_min[] = $item["date_time"];
                                    $constant_value_array_hinge_min[] = $item["value"];
                                }

                                // Now, $date_time_array contains the date_time values, and $constant_value_array contains the constant_value values
                                //print_r($date_time_array_hinge_min);
                                //print_r($constant_value_array_hinge_min);

                            // GET RATING TABLES
                                $rating_stage_coe = find_rating_stage_coe($db,$location_id_first_element);
                                $rating_stage_usgs = find_rating_stage_usgs($db,$location_id_first_element);
                                $rating_stage_nws = find_rating_stage_nws($db,$location_id_first_element);

                            // GET LOCATION LEVEL
                                $location_level = get_location_level($db, $location_id_first_element);

                                // Initialize a variable to store the result
                                $found_ngvd29_ojb = null;

                                // Loop through the array to find the object with the name 'Bob'
                                foreach ($location_level as $object) {
                                    if ($object->specified_level_id === 'NGVD29') {
                                        $found_ngvd29_ojb = $object;
                                        break; // Exit the loop once we've found the object
                                    }
                                }

                                // Check if we found the object and access its properties
                                if ($found_ngvd29_ojb !== null) {
                                    // Output the values
                                    //echo  $found_ngvd29_ojb->specified_level_id;
                                    //echo  $found_ngvd29_ojb->constant_level;
                                    //echo  $found_ngvd29_ojb->elevation;
                                } else {
                                    echo "Object with name 'specified_level_id_ngvd29' not found.";
                                }


                            // JSON string cwms_ts_id
                                $js_cwms_ts_id = json_encode($cwms_ts_id_first_element);
                                $js_cwms_ts_id_2 = json_encode($cwms_ts_id_2_first_element);
                                $js_cwms_ts_id_3 = json_encode($cwms_ts_id_3_first_element);
                                $js_cwms_ts_id_4 = json_encode($cwms_ts_id_4_first_element);
                                $js_cwms_ts_id_5 = json_encode($cwms_ts_id_5_first_element);
                                $js_cwms_ts_id_6 = json_encode($cwms_ts_id_6_first_element);
                                $js_cwms_ts_id_7 = json_encode($cwms_ts_id_7_first_element);


                            // JSON string start_day and end_day
                                $js_start_day = json_encode($start_day);
                                $js_end_day = json_encode($end_day);


                            // JSON string table_data
                                $table_data_json = json_encode($table_data);
                                $table_data_json_2 = json_encode($table_data_2);
                                $table_data_json_3 = json_encode($table_data_3);
                                $table_data_json_4 = json_encode($table_data_4);
                                $table_data_json_5 = json_encode($table_data_5);
                                $table_data_json_6 = json_encode($table_data_6);
                                $table_data_json_7 = json_encode($table_data_7);


                            // Associative array data
                                $data = json_decode($table_data_json, true);
                                $data_2 = json_decode($table_data_json_2, true);
                                $data_3 = json_decode($table_data_json_3, true);
                                $data_4 = json_decode($table_data_json_4, true);
                                $data_5 = json_decode($table_data_json_5, true);
                                $data_6 = json_decode($table_data_json_6, true);
                                $data_7 = json_decode($table_data_json_7, true);
                            ?>


                            <script>
                                // SETUP JS VAR
                                    // basin
                                    var basin_js = <?php echo json_encode($basin); ?>;
                                    console.log("basin_js: ", basin_js);

                                    // time series 1
                                    var cwms_ts_id_js = <?php echo json_encode($cwms_ts_id_first_element); ?>;
                                    console.log("cwms_ts_id_js: ", cwms_ts_id_js);

                                    var location_id_js = <?php echo json_encode($location_id_first_element); ?>;
                                    console.log("location_id_js: ", location_id_js);

                                    var parameter_id_js = <?php echo json_encode($parameter_id_first_element); ?>;
                                    console.log("parameter_id_js: ", parameter_id_js);

                                    var version_id_js = <?php echo json_encode($version_id_first_element); ?>;
                                    console.log("version_id_js: ", version_id_js);

                                    var unit_id_js = <?php echo json_encode($unit_id_first_element); ?>;
                                    console.log("unit_id_js: ", unit_id_js);

                                    // time series 2
                                    var cwms_ts_id_2_js = <?php echo json_encode($cwms_ts_id_2_first_element); ?>;
                                    console.log("cwms_ts_id_2_js: ", cwms_ts_id_2_js);

                                    // start day
                                    var start_day_js = <?php echo json_encode($start_day); ?>;
                                    console.log("start_day_js: ", start_day_js);

                                    // end day
                                    var end_day_js = <?php echo json_encode($end_day); ?>;
                                    console.log("end_day_js: ", end_day_js);
                            </script>


                            <script>
                                // LOCATION LEVEL SCRIPT
                                    var cwms_ts_id_rating = <?php echo json_encode($cwms_ts_id_first_element); ?>;
                                    console.log("cwms_ts_id_rating: ", cwms_ts_id_rating);

                                    var location_id_rating = <?php echo json_encode($location_id_first_element); ?>;
                                    console.log("location_id_rating: ", location_id_rating);

                                    var parameter_id_rating = <?php echo json_encode($parameter_id_first_element); ?>;
                                    console.log("parameter_id_rating: ", parameter_id_rating);

                                    var cwms_ts_id_2_rating = <?php echo json_encode($cwms_ts_id_2_first_element); ?>;
                                    console.log("cwms_ts_id_2_rating: ", cwms_ts_id_2_rating);

                                    var version_id_rating = <?php echo json_encode($version_id_first_element); ?>;
                                    console.log("version_id_rating: ", version_id_rating);

                                    var unit_id_rating = <?php echo json_encode($unit_id_first_element); ?>;
                                    console.log("unit_id_rating: ", unit_id_rating);

                                    // Initialize variables
                                    var myRatingCOEText = "";
                                    var myRatingUSGSText = "";
                                    var myRatingNWSText = "";
                                    var myNAV88Text = "";
                                    var myNGVD29Text = "";
                                    var myLocationLevelText = "";
                                    var myLocationLevelAllText = "";

                                    // Add hover underline style directly within the script
                                    // You can include the !important rule to ensure that your hover styles take precedence
                                    var hoverUnderlineStyle = "a:hover { text-decoration: underline !important; color: black !important;}";
                                    var styleElement = document.createElement("style");
                                    styleElement.type = "text/css";
                                    styleElement.appendChild(document.createTextNode(hoverUnderlineStyle));
                                    document.head.appendChild(styleElement);

                                    // FOR FLOW
                                    if (parameter_id_rating === "Flow") {
                                        var rating_coe = <?php echo json_encode($rating_stage_coe); ?>;
                                        console.log("rating_coe = ", rating_coe);
                                        var rating_usgs = <?php echo json_encode($rating_stage_usgs); ?>;
                                        console.log("rating_usgs = ", rating_usgs);
                                        var rating_nws = <?php echo json_encode($rating_stage_nws); ?>;
                                        console.log("rating_nws = ", rating_nws);
                                        if (rating_coe !== null) {
                                            myRatingCOEText = "<span style='margin-right: 15px;'>" + "<a href='https://wm.mvs.ds.usace.army.mil/web_apps/reports/public/rating_table.php?type=COE&location_id=" + rating_coe.location_id + "' target='_blank'>" + "RatingCOE" + "</a>" + "</span>";
                                            console.log("myRatingCOEText = ", myRatingCOEText);
                                        }
                                        if (rating_usgs !== null) {
                                            myRatingUSGSText = "<span style='margin-right: 15px;'>" + "<a href='https://wm.mvs.ds.usace.army.mil/web_apps/reports/public/rating_table.php?type=USGS&location_id=" + rating_usgs.location_id + "' target='_blank'>" + "RatingUSGS" + "</a>" + "</span>";
                                            console.log("myRatingUSGSText = ", myRatingUSGSText);
                                        }
                                        if (rating_nws !== null) {
                                            myRatingNWSText = "<span style='margin-right: 15px;'>" + "<a href='https://wm.mvs.ds.usace.army.mil/web_apps/reports/public/rating_table.php?type=NWS&location_id=" + rating_nws.location_id + "' target='_blank'>" + "RatingNWS" + "</a>" + "</span>";
                                            console.log("myRatingNWSText = ", myRatingNWSText);
                                        }
                                    // FOR STAGE
                                    } else if (parameter_id_rating === "Stage" || parameter_id_rating === "Elev") {
                                        var location_level = <?php echo json_encode($location_level); ?>;
                                        console.log("location_level = ", location_level);
                                        var found_ngvd29_ojb = <?php echo json_encode($found_ngvd29_ojb); ?>;
                                        console.log("found_ngvd29_ojb = ", found_ngvd29_ojb);
                                        // STAGE29 
                                        if (version_id_rating === "29") {
                                            if (location_level !== null) {
                                                myNAV88Text = "<span style='margin-right: 15px;' title='Add " + (parseFloat(found_ngvd29_ojb.elevation) - parseFloat(found_ngvd29_ojb.constant_level)).toFixed(2) + " " + found_ngvd29_ojb.level_unit + " to obtain Elev NAVD88'>" + "<a href='https://wm.mvs.ds.usace.army.mil/web_apps/reports/public/datum_conversion.php' target='_blank'>" + "NAVD88 = " + (parseFloat(found_ngvd29_ojb.elevation) - parseFloat(found_ngvd29_ojb.constant_level)).toFixed(2) + found_ngvd29_ojb.level_unit + "</a>" + "</span>";
                                                console.log("myNAV88Text = ", myNAV88Text);
                                                myNGVD29Text = "<span style='margin-right: 15px;' title='Add " + (parseFloat(found_ngvd29_ojb.constant_level) - parseFloat(found_ngvd29_ojb.constant_level)).toFixed(2) + " " + found_ngvd29_ojb.level_unit + " to obtain Elev NGVD29'>" + "<a href='https://wm.mvs.ds.usace.army.mil/web_apps/reports/public/location_level.php?specified_level_id=NGVD29' target='_blank'>" + found_ngvd29_ojb.specified_level_id + " = " + (parseFloat(found_ngvd29_ojb.constant_level) - parseFloat(found_ngvd29_ojb.constant_level)).toFixed(2) + found_ngvd29_ojb.level_unit + "</a>" + "</span>";
                                                console.log("myNGVD29Text = ", myNGVD29Text);
                                            } else {

                                            }
                                        // STAGE
                                        } else if (version_id_rating === "lrgsShef-rev") {
                                            if (location_level !== null) {
                                                myNAV88Text = "<span style='margin-right: 15px;' title='Add " + (parseFloat(found_ngvd29_ojb.elevation)).toFixed(2) + " " + found_ngvd29_ojb.level_unit + " to obtain Elev NAVD88'>" + "<a href='https://wm.mvs.ds.usace.army.mil/web_apps/reports/public/datum_conversion.php' target='_blank'>" + "NAVD88 = " + (parseFloat(found_ngvd29_ojb.elevation)).toFixed(2) + found_ngvd29_ojb.level_unit + "</a>" + "</span>";
                                                console.log("myNAV88Text = ", myNAV88Text);
                                                myNGVD29Text = "<span style='margin-right: 15px;' title='Add " + (parseFloat(found_ngvd29_ojb.constant_level)).toFixed(2) + " " + found_ngvd29_ojb.level_unit + " to obtain Elev NGVD29'>" + "<a href='https://wm.mvs.ds.usace.army.mil/web_apps/reports/public/location_level.php?specified_level_id=NGVD29' target='_blank'>" + found_ngvd29_ojb.specified_level_id + " = " + (parseFloat(found_ngvd29_ojb.constant_level)).toFixed(2) + found_ngvd29_ojb.level_unit + "</a>" + "</span>";
                                                console.log("myNGVD29Text = ", myNGVD29Text);
                                            } else {

                                            }
                                        } else {

                                        }
                                        // DISPLAY LOCATION LEVEL FLOOD AND LWRP ONLY
                                        location_level.forEach(level => {
                                            if ((level.specified_level_id === "Flood" || level.specified_level_id === "LWRP") && parseFloat(level.constant_level).toFixed(2) < 900) {
                                                myLocationLevelText += "<span style='margin-right: 15px;'>" + "<a href='https://wm.mvs.ds.usace.army.mil/web_apps/reports/public/location_level.php?specified_level_id=" + level.specified_level_id + "' target='_blank' style='text-decoration: none;'>" + level.specified_level_id + " = " + parseFloat(level.constant_level).toFixed(2) + level.level_unit + "</a>" + "</span>";
                                                console.log("myLocationLevelText = ", myLocationLevelText);
                                            } else {
                                                // Handle other cases if needed
                                            }
                                        });


                                        // DISPLAY ALL LOCATION LEVEL
                                        location_level.forEach(level => {
                                            if ((level.specified_level_id !== "Flood" && level.specified_level_id !== "LWRP" && level.specified_level_id !== "NGVD29") &&  parseFloat(level.constant_level).toFixed(2) < 900) {
                                                myLocationLevelAllText += "<span style='margin-right: 15px;'>" + "<a href='https://wm.mvs.ds.usace.army.mil/web_apps/reports/public/location_level.php?specified_level_id=" + level.specified_level_id + "' target='_blank'>" + level.specified_level_id + " = " + parseFloat(level.constant_level).toFixed(2) + level.level_unit + "</a>" + "</span>";
                                                console.log("myLocationLevelAllText = ", myLocationLevelAllText);
                                            } else {
                                                // Handle other cases if needed
                                            }
                                        }); 
                                    } else {

                                    }

                                    var metaDataDiv = document.getElementById("metaData");
                                    var metadataHTML = "";
                                    if (myRatingCOEText || myRatingUSGSText || myRatingNWSText) {
                                        metadataHTML += myRatingCOEText + "  " + myRatingUSGSText + "  " + myRatingNWSText;
                                    } 
                                    if (myNAV88Text || myNGVD29Text) {
                                        metadataHTML += myNAV88Text + "  " + myNGVD29Text ;
                                    } 
                                    if (myLocationLevelText) {
                                        metadataHTML += myLocationLevelText;
                                    } 
                                    if (myLocationLevelAllText) {
                                        metadataHTML += myLocationLevelAllText;
                                    } 

                                    console.log("metadataHTML = ", metadataHTML);

                                    // Check if any metadata is available
                                    if (metadataHTML !== "" && cwms_ts_id_2_rating === null) {
                                        metaDataDiv.innerHTML = metadataHTML;
                                    } else {
                                        metaDataDiv.innerHTML = "No metadata available or disabled for multiple time series plot.";
                                    }
                            </script>


                            <script>
                                // UPDATE PLOT
                                    // To set the default values of selectStartDay and selectEndDay to start_day_js and end_day_js when the page is opened, you can modify the script as follows:
                                    document.addEventListener("DOMContentLoaded", function () {
                                        // Set default values for selectStartDay and selectEndDay
                                        document.getElementById('selectStartDay').value = start_day_js.toString();
                                        document.getElementById('selectEndDay').value = end_day_js.toString();
                                    });

                                    function populateDropdown(id, value) {
                                        console.log("populateDropdown: " + id + " = ", value);
                                        var dropdown = document.getElementById(id);
                                        console.log("dropdown = ", dropdown);
                                        var option = document.createElement('option');
                                        option.value = value;
                                        option.text = value;
                                        dropdown.add(option);
                                    }

                                    var cwms_ts_id_dropdown = <?php echo json_encode($cwms_ts_id_first_element); ?>;
                                    console.log("cwms_ts_id_dropdown: ", cwms_ts_id_dropdown);

                                    var cwms_ts_id_2_dropdown = <?php echo json_encode($cwms_ts_id_2_first_element); ?>;
                                    console.log("cwms_ts_id_2_dropdown: ", cwms_ts_id_2_dropdown);

                                    var cwms_ts_id_3_dropdown = <?php echo json_encode($cwms_ts_id_3_first_element); ?>;
                                    console.log("cwms_ts_id_3_dropdown: ", cwms_ts_id_3_dropdown);

                                    var cwms_ts_id_4_dropdown = <?php echo json_encode($cwms_ts_id_4_first_element); ?>;
                                    console.log("cwms_ts_id_4_dropdown: ", cwms_ts_id_4_dropdown);

                                    var cwms_ts_id_5_dropdown = <?php echo json_encode($cwms_ts_id_5_first_element); ?>;
                                    console.log("cwms_ts_id_5_dropdown: ", cwms_ts_id_5_dropdown);

                                    var cwms_ts_id_6_dropdown = <?php echo json_encode($cwms_ts_id_6_first_element); ?>;
                                    console.log("cwms_ts_id_6_dropdown: ", cwms_ts_id_6_dropdown);

                                    var cwms_ts_id_7_dropdown = <?php echo json_encode($cwms_ts_id_7_first_element); ?>;
                                    console.log("cwms_ts_id_7_dropdown: ", cwms_ts_id_7_dropdown);

                                    var hideTimeSeries = 'hidden';

                                    var basinSelected = 'na';

                                    // basin
                                    if (basin_js !== null) {
                                        populateDropdown('selectedBasinUpdatePlot', basin_js);
                                    } else {
                                        populateDropdown('selectedBasinUpdatePlot', basinSelected);
                                    }

                                    // cwms_ts_id
                                    if (cwms_ts_id_js !== null) {
                                        populateDropdown('selectedTimeSeries01', cwms_ts_id_dropdown);
                                        if (cwms_ts_id_2_dropdown !== null) {
                                            populateDropdown('selectedTimeSeries01', cwms_ts_id_2_dropdown);
                                        }
                                        if (cwms_ts_id_3_dropdown !== null) {
                                            populateDropdown('selectedTimeSeries01', cwms_ts_id_3_dropdown);
                                        }
                                        if (cwms_ts_id_4_dropdown !== null) {
                                            populateDropdown('selectedTimeSeries01', cwms_ts_id_4_dropdown);
                                        }
                                        if (cwms_ts_id_5_dropdown !== null) {
                                            populateDropdown('selectedTimeSeries01', cwms_ts_id_5_dropdown);
                                        }
                                        if (cwms_ts_id_6_dropdown !== null) {
                                            populateDropdown('selectedTimeSeries01', cwms_ts_id_6_dropdown);
                                        }
                                        if (cwms_ts_id_7_dropdown !== null) {
                                            populateDropdown('selectedTimeSeries01', cwms_ts_id_7_dropdown);
                                        }
                                    }

                                    // cwms_ts_id_2
                                    if (cwms_ts_id_2_js !== null) {
                                        populateDropdown('selectedTimeSeries02', cwms_ts_id_2_dropdown);
                                        populateDropdown('selectedTimeSeries02', cwms_ts_id_dropdown);
                                        populateDropdown('selectedTimeSeries02', cwms_ts_id_3_dropdown);
                                        populateDropdown('selectedTimeSeries02', cwms_ts_id_4_dropdown);
                                        populateDropdown('selectedTimeSeries02', cwms_ts_id_5_dropdown);
                                        populateDropdown('selectedTimeSeries02', cwms_ts_id_6_dropdown);
                                        populateDropdown('selectedTimeSeries02', cwms_ts_id_7_dropdown);
                                        populateDropdown('selectedTimeSeries02', hideTimeSeries);
                                    }

                                    // cwms_ts_id_3
                                    if (cwms_ts_id_3_dropdown !== null) {
                                        populateDropdown('selectedTimeSeries03', cwms_ts_id_3_dropdown);
                                        populateDropdown('selectedTimeSeries03', cwms_ts_id_dropdown);
                                        populateDropdown('selectedTimeSeries03', cwms_ts_id_2_dropdown);
                                        populateDropdown('selectedTimeSeries03', cwms_ts_id_4_dropdown);
                                        populateDropdown('selectedTimeSeries03', cwms_ts_id_5_dropdown);
                                        populateDropdown('selectedTimeSeries03', cwms_ts_id_6_dropdown);
                                        populateDropdown('selectedTimeSeries03', cwms_ts_id_7_dropdown);
                                        populateDropdown('selectedTimeSeries03', hideTimeSeries);
                                    }

                                    // cwms_ts_id_4
                                    if (cwms_ts_id_4_dropdown !== null) {
                                        populateDropdown('selectedTimeSeries04', cwms_ts_id_4_dropdown);
                                        populateDropdown('selectedTimeSeries04', cwms_ts_id_dropdown);
                                        populateDropdown('selectedTimeSeries04', cwms_ts_id_2_dropdown);
                                        populateDropdown('selectedTimeSeries04', cwms_ts_id_3_dropdown);
                                        populateDropdown('selectedTimeSeries04', cwms_ts_id_5_dropdown);
                                        populateDropdown('selectedTimeSeries04', cwms_ts_id_6_dropdown);
                                        populateDropdown('selectedTimeSeries04', cwms_ts_id_7_dropdown);
                                        populateDropdown('selectedTimeSeries04', hideTimeSeries);
                                    }

                                    // cwms_ts_id_5
                                    if (cwms_ts_id_5_dropdown !== null) {
                                        populateDropdown('selectedTimeSeries05', cwms_ts_id_5_dropdown);
                                        populateDropdown('selectedTimeSeries05', cwms_ts_id_dropdown);
                                        populateDropdown('selectedTimeSeries05', cwms_ts_id_2_dropdown);
                                        populateDropdown('selectedTimeSeries05', cwms_ts_id_3_dropdown);
                                        populateDropdown('selectedTimeSeries05', cwms_ts_id_4_dropdown);
                                        populateDropdown('selectedTimeSeries05', cwms_ts_id_6_dropdown);
                                        populateDropdown('selectedTimeSeries05', cwms_ts_id_7_dropdown);
                                        populateDropdown('selectedTimeSeries05', hideTimeSeries);
                                    }

                                    // cwms_ts_id_6
                                    if (cwms_ts_id_6_dropdown !== null) {
                                        populateDropdown('selectedTimeSeries06', cwms_ts_id_6_dropdown);
                                        populateDropdown('selectedTimeSeries06', cwms_ts_id_dropdown);
                                        populateDropdown('selectedTimeSeries06', cwms_ts_id_2_dropdown);
                                        populateDropdown('selectedTimeSeries06', cwms_ts_id_3_dropdown);
                                        populateDropdown('selectedTimeSeries06', cwms_ts_id_4_dropdown);
                                        populateDropdown('selectedTimeSeries06', cwms_ts_id_5_dropdown);
                                        populateDropdown('selectedTimeSeries06', cwms_ts_id_7_dropdown);
                                        populateDropdown('selectedTimeSeries06', hideTimeSeries);
                                    }

                                    // cwms_ts_id_7
                                    if (cwms_ts_id_7_dropdown !== null) {
                                        populateDropdown('selectedTimeSeries07', cwms_ts_id_7_dropdown);
                                        populateDropdown('selectedTimeSeries07', cwms_ts_id_dropdown);
                                        populateDropdown('selectedTimeSeries07', cwms_ts_id_2_dropdown);
                                        populateDropdown('selectedTimeSeries07', cwms_ts_id_3_dropdown);
                                        populateDropdown('selectedTimeSeries07', cwms_ts_id_4_dropdown);
                                        populateDropdown('selectedTimeSeries07', cwms_ts_id_5_dropdown);
                                        populateDropdown('selectedTimeSeries07', cwms_ts_id_6_dropdown);
                                        populateDropdown('selectedTimeSeries07', hideTimeSeries);
                                    }

                                    function getUpdatedPlotValues() {
                                        var selectedBasinDropdown = document.getElementById('selectedBasinUpdatePlot').value;
                                        console.log("selectedBasinDropdown: ", selectedBasinDropdown);

                                        var selectedTimeSeries01 = document.getElementById('selectedTimeSeries01').value;
                                        console.log("selectedTimeSeries01: ", selectedTimeSeries01);

                                        var selectedTimeSeries02 = cwms_ts_id_2_dropdown !== null ? document.getElementById('selectedTimeSeries02').value : null;
                                        if (selectedTimeSeries02 !== null) {
                                            console.log("selectedTimeSeries02: ", selectedTimeSeries02);
                                        }

                                        var selectedTimeSeries03 = cwms_ts_id_3_dropdown !== null ? document.getElementById('selectedTimeSeries03').value : null;
                                        if (selectedTimeSeries03 !== null) {
                                            console.log("selectedTimeSeries03: ", selectedTimeSeries03);
                                        }

                                        var selectedTimeSeries04 = cwms_ts_id_4_dropdown !== null ? document.getElementById('selectedTimeSeries04').value : null;
                                        if (selectedTimeSeries04 !== null) {
                                            console.log("selectedTimeSeries04: ", selectedTimeSeries04);
                                        }

                                        var selectedTimeSeries05 = cwms_ts_id_5_dropdown !== null ? document.getElementById('selectedTimeSeries05').value : null;
                                        if (selectedTimeSeries05 !== null) {
                                            console.log("selectedTimeSeries05: ", selectedTimeSeries05);
                                        }

                                        var selectedTimeSeries06 = cwms_ts_id_6_dropdown !== null ? document.getElementById('selectedTimeSeries06').value : null;
                                        if (selectedTimeSeries06 !== null) {
                                            console.log("selectedTimeSeries06: ", selectedTimeSeries06);
                                        }

                                        var selectedTimeSeries07 = cwms_ts_id_7_dropdown !== null ? document.getElementById('selectedTimeSeries07').value : null;
                                        if (selectedTimeSeries07 !== null) {
                                            console.log("selectedTimeSeries07: ", selectedTimeSeries07);
                                        }

                                        // Repeat for other dropdowns as needed
                                        var selectStartDay = document.getElementById('selectStartDay').value || '4'; // Default to 4 if no value selected
                                        var selectEndDay = document.getElementById('selectEndDay').value || '0'; // Default to 0 if no value selected

                                        // Construct the URL with selected values
                                        var redirectURL = 'https://wm.mvs.ds.usace.army.mil/web_apps/plot_macro/public/plot_macro.php?' +
                                            'basin=' + encodeURIComponent(selectedBasinDropdown) +
                                            '&cwms_ts_id=' + encodeURIComponent(selectedTimeSeries01);

                                        // Add selectedTimeSeries02, selectedTimeSeries03, etc. only if they are not null
                                        if (selectedTimeSeries02 !== null) {
                                            redirectURL += '&cwms_ts_id_2=' + encodeURIComponent(selectedTimeSeries02);
                                        }

                                        if (selectedTimeSeries03 !== null) {
                                            redirectURL += '&cwms_ts_id_3=' + encodeURIComponent(selectedTimeSeries03);
                                        }

                                        if (selectedTimeSeries04 !== null) {
                                            redirectURL += '&cwms_ts_id_4=' + encodeURIComponent(selectedTimeSeries04);
                                        }

                                        if (selectedTimeSeries05 !== null) {
                                            redirectURL += '&cwms_ts_id_5=' + encodeURIComponent(selectedTimeSeries05);
                                        }

                                        if (selectedTimeSeries06 !== null) {
                                            redirectURL += '&cwms_ts_id_6=' + encodeURIComponent(selectedTimeSeries06);
                                        }

                                        if (selectedTimeSeries07 !== null) {
                                            redirectURL += '&cwms_ts_id_7=' + encodeURIComponent(selectedTimeSeries07);
                                        }

                                        redirectURL += '&start_day=' + encodeURIComponent(selectStartDay) + '&end_day=' + encodeURIComponent(selectEndDay);

                                        // Log the constructed URL for debugging
                                        console.log("redirectURL: ", redirectURL);

                                        // Redirect to the constructed URL
                                        window.location.href = redirectURL;
                                    }
                            </script>


                            <script>
                                // SWITCH BASIN
                                    const basins = ["Mississippi", "Illinois", "Missouri", "Meramec", "Tributaries", "Mark Twain DO", "Mark Twain", "Wappapello", "Shelbyville", "Carlyle", "Rend", "Kaskaskia Nav", "Water Quality"];

                                    const cwms_ts_id_selection = {
                                        "Mississippi": ["St Louis-Mississippi.Stage.Inst.30Minutes.0.lrgsShef-rev"],
                                        "Illinois": ["Meredosia-Illinois.Stage.Inst.30Minutes.0.lrgsShef-rev"],
                                        "Missouri": ["St Charles-Missouri.Stage.Inst.30Minutes.0.lrgsShef-rev"],
                                        "Meramec": ["Eureka-Meramec.Flow.Inst.30Minutes.0.RatingUSGS"],
                                        "Tributaries": ["Troy-Cuivre.Flow.Inst.15Minutes.0.RatingUSGS"],
                                        "Mark%20Twain%20DO": ["Mark Twain Lk TW-Salt.Conc-DO.Inst.15Minutes.0.lrgsShef-raw"],
                                        "Mark%20Twain": ["Mark Twain Lk-Salt.Stage.Inst.30Minutes.0.29"],
                                        "Wappapello": ["Wappapello Lk-St Francis.Stage.Inst.30Minutes.0.29"],
                                        "Shelbyville": ["Lk Shelbyville-Kaskaskia.Stage.Inst.30Minutes.0.29"],
                                        "Carlyle": ["Carlyle Lk-Kaskaskia.Stage.Inst.30Minutes.0.29"],
                                        "Rend": ["Rend Lk-Big Muddy.Stage.Inst.30Minutes.0.29"],
                                        "Kaskaskia%20Nav": ["Venedy Station-Kaskaskia.Flow.Inst.15Minutes.0.RatingUSGS"],
                                        "Water%20Quality": ["Mark Twain Lk TW-Salt.Conc-DO.Inst.15Minutes.0.lrgsShef-raw"],
                                    };

                                    function populateSwitchBasinDropdown(selectSwitchBasinBox, options) {
                                        selectSwitchBasinBox.innerHTML = '';
                                        options.forEach(option => {
                                            // Ensure option is a string before calling trim
                                            const optionString = String(option);
                                            const trimmedOption = optionString.trim();
                                            const optionElem = document.createElement('option');
                                            optionElem.value = trimmedOption;
                                            //optionElem.value = trimmedOption.replace(/ /g, '%20');
                                            optionElem.text = trimmedOption;
                                            selectSwitchBasinBox.appendChild(optionElem);
                                        });
                                    }

                                    function populateSwitchBasinSecondDropdown() {
                                        const selectSwitchBasinBox = document.getElementById('selectSwitchBasinBox');
                                        //const selectedBasin = selectSwitchBasinBox.value.trim();
                                        const selectedBasin = selectSwitchBasinBox.value.trim().replace(/ /g, '%20');
                                        const selectSwitchBasinTimeSeriesBox = document.getElementById('selectSwitchBasinTimeSeriesBox');
                                        console.log('selectedBasin:', selectedBasin);

                                        // Clear existing options in the second dropdown
                                        selectSwitchBasinTimeSeriesBox.innerHTML = '';

                                        // Convert all keys in cwms_ts_id_selection to lowercase
                                        const cwms_ts_id_formatted = Object.fromEntries(
                                            Object.entries(cwms_ts_id_selection).map(([key, value]) => [key, value])
                                        );

                                        // Log the entire cwms_ts_id_selection object for inspection
                                        console.log('cwms_ts_id_formatted:', cwms_ts_id_formatted);

                                        // Populate options for the selected basin from the cwms_ts_id_selection object
                                        const measurementOptions = cwms_ts_id_formatted[selectedBasin] || [];
                                        console.log('cwms_ts_id Options:', measurementOptions);

                                        populateSwitchBasinDropdown(selectSwitchBasinTimeSeriesBox, measurementOptions);
                                    }

                                    document.addEventListener('DOMContentLoaded', function () {
                                        const selectSwitchBasinBox = document.getElementById('selectSwitchBasinBox');
                                        const selectSwitchBasinTimeSeriesBox = document.getElementById('selectSwitchBasinTimeSeriesBox');

                                        // Populate basins dropdown
                                        populateSwitchBasinDropdown(selectSwitchBasinBox, basins);

                                        // Set a default basin selection for testing
                                        selectSwitchBasinBox.value = basin_js;

                                        // Populate the cwms_ts_id dropdown based on the default selected basin
                                        populateSwitchBasinSecondDropdown();

                                        // Set up event listener for basin dropdown change
                                        selectSwitchBasinBox.addEventListener('change', function () {
                                            populateSwitchBasinSecondDropdown();
                                        });

                                        // Trigger the population of the second and third dropdowns on page load
                                        selectSwitchBasinBox.dispatchEvent(new Event('change'));
                                    });

                                    function getUpdatedSwitchBasinValues() {
                                        const selectSwitchBasinBox = document.getElementById('selectSwitchBasinBox');
                                        const selectSwitchBasinValue = selectSwitchBasinBox.value;

                                        const selectSwitchBasinTimeSeriesBox = document.getElementById('selectSwitchBasinTimeSeriesBox');
                                        const selectSwitchBasinTimeSeriesValue = selectSwitchBasinTimeSeriesBox.value;

                                        // Construct the URL based on selected variables
                                        const redirectURL = `https://wm.mvs.ds.usace.army.mil/web_apps/plot_macro/public/plot_macro.php?basin=${selectSwitchBasinValue}&cwms_ts_id=${selectSwitchBasinTimeSeriesValue}&start_day=4&end_day=0`;

                                        // Redirect to the new URL
                                        window.location.href = redirectURL;
                                    }
                            </script>


                            <script> 
                                // METADATA
                                    // Function to make an Ajax request
                                    function getMetaData(cwms_ts_id_js) {
                                        // Check if the result div is already populated
                                        var resultDiv = document.getElementById("result");
                                        var button = document.querySelector(".modern-button-meta");

                                        console.log("Button Clicked!"); // Add this line for debugging

                                        if (resultDiv.innerHTML.trim() !== "") {
                                            // If the div is not empty, clear its content
                                            resultDiv.innerHTML = "";
                                            button.textContent = "Load MetaData"; // Set button text to "Get Data"
                                            return; // Exit the function without making an AJAX request
                                        }

                                        var xhr = new XMLHttpRequest();

                                        xhr.onreadystatechange = function () {
                                            if (xhr.readyState == 4 && xhr.status == 200) {
                                                console.log("Request Successful!");
                                                // If the request was successful, update the result div
                                                var responseData = JSON.parse(xhr.responseText);

                                                console.log("Data Response: ", responseData);

                                                // Access the location_id property (NEED THIS TO CALL METADATA)
                                                var locationId = responseData.location_id;

                                                // Clear the previous content in the result div
                                                resultDiv.innerHTML = "";

                                                if (cwms_ts_id_2_rating === null) {
                                                    resultDiv.innerHTML = "<br>" + "Location ID: " + responseData.location_id;
                                                    getMoreData(locationId,responseData);
                                                } else {
                                                    resultDiv.innerHTML = "Disabled for multiple time series plot.";
                                                }

                                                // Change the button text to "Clear Data"
                                                button.textContent = "Clear MetaData";
                                                console.log("Button Text After Change: " + button.textContent);
                                            } else {
                                                console.log("Loading Metadata. Please wait...");
                                                // Handle the error scenario, e.g., display an error message
                                                resultDiv.innerHTML = "Loading metadata. Please wait..." + "<br>" + "<img src='images/loading4.gif' style='height: 30px; width: 30px;'>";
                                            }
                                        };

                                        // Append the parameter to the URL or modify the request based on your requirements
                                        var url = "get_gage_data_page.php?cwms_ts_id_js=" + encodeURIComponent(cwms_ts_id_js);

                                        // Make a GET request to getMetaData.php
                                        xhr.open("GET", url, true);
                                        xhr.send();
                                    }

                                    // Function to get more data based on location_id
                                    function getMoreData(locationId,responseData) {
                                        var xhr = new XMLHttpRequest();

                                        xhr.onreadystatechange = function () {
                                            if (xhr.readyState == 4 && xhr.status == 200) {
                                                // If the request was successful, update the result div
                                                var responseData2 = JSON.parse(xhr.responseText);

                                                console.log("More Data Response: ", responseData2);

                                                // If the request was successful, append the responseText to the result div
                                                //document.getElementById("result").innerHTML += "<br><br>" + "More Data Response: " + xhr.responseText;
                                    
                                                // If responseData2 is an array, access the first element and then the latitude property
                                                if (Array.isArray(responseData2) && responseData2.length > 0) {
                                                    var latitude = responseData2[0].latitude;
                                                    var longitude = responseData2[0].longitude;
                                                    var elevation = parseFloat(responseData2[0].elevation).toFixed(2);
                                                    var vertical_datum = responseData2[0].vertical_datum;
                                                    var unit_id = responseData2[0].unit_id;

                                                    //document.getElementById("result").innerHTML += "<br><br>" + "Lat: " + responseData2[0].latitude;
                                                    
                                                    //google maps
                                                    document.getElementById("result").innerHTML += "<br><br>" + "<a href='http://maps.google.com/maps?&z=20&mrt=yp&t=k&q=" + latitude + "+" + longitude + "' target='_blank' title='Google Maps'>" + "View on Google Maps</a>";

                                                    // Latitude and Longitude
                                                    document.getElementById("result").innerHTML += "<br><br>" + "<a href='../../reports/public/metadata_lat_long.php' target='_blank'>" + "Latitude and Longitude" + "</a>";

                                                    // Gage Zero
                                                    // document.getElementById("result").innerHTML += "<br><br>" + "<span title='CWMS Elevation'>" + "<a href='../../reports/public/datum_conversion.php' target='_blank'>" + "Gage Zero = " + elevation + unit_id + " " + vertical_datum  + "</a>" + "</span>";

                                                    // Datum Conversion
                                                    document.getElementById("result").innerHTML += "<br><br>" + "<a href='../../reports/public/conversion_29_88.php' target='_blank'>" + "Datum Conversion" + "</a>";
                                                
                                                    // Call the function to add more HTML content
                                                    addMoreHtmlContent(responseData);
                                                }
                                            } else {
                                                // Handle the error scenario, e.g., display an error message
                                                resultDiv.innerHTML = "Loading more data. Please wait..." + "<br>" + "<img src='images/loading4.gif' style='height: 30px; width: 30px;'>";
                                            }
                                        };

                                        // Append the location_id as a parameter to the URL
                                        var moreDataUrl = "get_metadata.php?location_id=" + encodeURIComponent(locationId);

                                        // Make a GET request to get_more_data.php
                                        xhr.open("GET", moreDataUrl, true);
                                        xhr.send();
                                    }

                                    // Function to add more HTML content to the result div
                                    function addMoreHtmlContent(responseData) {
                                        var resultDiv = document.getElementById("result");

                                        // record stage
                                        resultDiv.innerHTML += "<br><br>" + "<span title='taken into account datman and Stage data'>" + "<a href='../../reports/public/metadata_record_stage2.php?location_id=" + responseData.location_id + "' target='_blank'>" + "Record Stage" + "</a>" + "</span>";

                                        // download dss
                                        if (responseData.owner === "MVS") { 
                                        resultDiv.innerHTML += "<br><br>" + "<a href='../../../data_manager/downloads/dss/MVS-Lite/" + responseData.location_id + ".csv.dss' target='_blank'>" + "Download DSS" + "</a>";
                                        }

                                        // min max
                                        resultDiv.innerHTML += "<br><br>" + "<a href='../../reports/public/alarm_location_id.php?location_id=" + responseData.location_id + "'target=_blank'>Min Max Display</a>";

                                        // missing data
                                        resultDiv.innerHTML += "<br><br>" + "<a href='../../reports/public/location_id_missing_data.php?location_id=" + responseData.location_id + "'target=_blank'>Missing Data</a>";
                                    }
                                
                            </script>


                            <script>
                                // DATMAN SECTION
                                    // Function to make an Ajax request
                                    function getDataDatman(cwms_ts_id_js) {
                                        // Check if the result div is already populated
                                        var resultDatmanDiv = document.getElementById("resultDatman");
                                        var button = document.querySelector(".modern-button-datman");

                                        if (resultDatmanDiv.innerHTML.trim() !== "") {
                                            // If the div is not empty, clear its content
                                            resultDatmanDiv.innerHTML = "";
                                            button.textContent = "Load Datman MetaData"; // Set button text to "Load Dataman MetaData"
                                            return; // Exit the function without making an AJAX request
                                        }

                                        var xhr = new XMLHttpRequest();

                                        xhr.onreadystatechange = function () {
                                            if (xhr.readyState == 4 && xhr.status == 200) {
                                                // If the request was successful, update the result div
                                                var responseDatmanData = JSON.parse(xhr.responseText);

                                                console.log("responseDatmanData: ", responseDatmanData);

                                                // Access the location_id property (NEED THIS TO CALL METADATA)
                                                var locationIdDatman = responseDatmanData.location_id;
                                                console.log("locationIdDatman: ", locationIdDatman);

                                                var datmanDatman = responseDatmanData.datman;
                                                console.log("datmanDatman: ", datmanDatman);

                                                var datmanCwmstsidDatman = responseDatmanData.datman_cwms_ts_id;
                                                console.log("datmanCwmstsidDatman: ", datmanCwmstsidDatman);

                                                var stageCwmstsidDatman = responseDatmanData.stage_cwms_ts_id;
                                                console.log("stageCwmstsidDatman: ", stageCwmstsidDatman);

                                                var stage29CwmstsidDatman = responseDatmanData.stage29_cwms_ts_id;
                                                console.log("stage29CwmstsidDatman: ", stage29CwmstsidDatman);

                                                // Clear the previous content in the result div
                                                resultDatmanDiv.innerHTML = "";

                                                // FOR SINGLE PLOT ONLY
                                                if (cwms_ts_id_2_js === null) {
                                                    if (datmanCwmstsidDatman !== null) {
                                                        // Datman data editing link
                                                        resultDatmanDiv.innerHTML = "<br>" + "<a href='../../reports/public/datman_data_editing_status.php' target='_blank'>" + "Datman Data Editing Status" + "</a>";
                                                        
                                                        // Top10 link
                                                        resultDatmanDiv.innerHTML += "<br><br>" + "<a href='../../reports/public/datman_data_editing_status.php?top10=True' target='_blank'>" + "Top 10" + "</a>";
                                                        
                                                        // Timestep link
                                                        if (version_id_js === "29") {
                                                            resultDatmanDiv.innerHTML += "<br><br>" + "<a href='../../reports/public/delta_timestep_list.php?location_id=" + locationIdDatman + "&cwms_ts_id=" + stageCwmstsidDatman + "&datman_parameter_id=" + "Elev" + "' target='_blank'>" + "Delta TimeStep" + "</a>";
                                                        } else if (version_id_js === "lrgsShef-rev") {
                                                            resultDatmanDiv.innerHTML += "<br><br>" + "<a href='../../reports/public/delta_timestep_list.php?location_id=" + locationIdDatman + "&cwms_ts_id=" + stageCwmstsidDatman + "&datman_parameter_id=" + "Stage" + "' target='_blank'>" + "Delta TimeStep" + "</a>";
                                                        } else {

                                                        }
                                                        console.log("resultDatmanDiv: ", resultDatmanDiv);
                                                        getMoreDataDatman(locationIdDatman, responseDatmanData, resultDatmanDiv); 
                                                    } else {
                                                        resultDatmanDiv.innerHTML = "Datman Time Series Not Available";
                                                    }
                                                } else {
                                                    resultDatmanDiv.innerHTML = "Disabled for multiple time series plot.";
                                                }

                                                // Change the button text to "Clear Data"
                                                button.textContent = "Clear Datman MetaData";
                                            } else {
                                                // Handle the error scenario, e.g., display an error message
                                                resultDatmanDiv.innerHTML = "Loading datman data. Please wait..." + "<br>" + "<img src='images/loading4.gif' style='height: 30px; width: 30px;'>";
                                            }
                                        };

                                        // Append the parameter to the URL or modify the request based on your requirements
                                        var url = "get_gage_data_page.php?cwms_ts_id_js=" + encodeURIComponent(cwms_ts_id_js);

                                        // Make a GET request to getData.php
                                        xhr.open("GET", url, true);
                                        xhr.send();
                                    }

                                    
                                    // Function to make another Ajax request
                                    function getMoreDataDatman(locationIdDatman, responseDatmanData, resultDatmanDiv) {
                                        var xhr2 = new XMLHttpRequest();

                                        xhr2.onreadystatechange = function () {
                                            if (xhr2.readyState == 4 && xhr2.status == 200) {
                                                // Handle the response of the second Ajax call here
                                                var responseMoreDatmanData = JSON.parse(xhr2.responseText);

                                                // Example: Log the data to the console
                                                console.log("responseMoreDatmanData:", responseMoreDatmanData);
                                                console.log("responseDatmanData More Data:", responseDatmanData);

                                                // Datman data spike link
                                                resultDatmanDiv.innerHTML += "<br><br>" + "<a href='../../reports/public/data_spike.php?cwms_ts_id=" + responseDatmanData.datman_cwms_ts_id + "' target='_blank'>" + "Data Spike Datman" + "</a>";

                                                if (responseDatmanData.display_stage29 === "True") {
                                                    // Stage29 data spike link
                                                    resultDatmanDiv.innerHTML += "<br><br>" + "<a href='../../reports/public/data_spike.php?cwms_ts_id=" + responseDatmanData.stage29_cwms_ts_id + "&location_id=" + responseDatmanData.location_id + "&latest_time=" + responseMoreDatmanData + "' target='_blank'>" + "Data Spike Stage29" + "</a>"; 
                                                } else {
                                                    // StageRev data spike link
                                                    resultDatmanDiv.innerHTML += "<br><br>" + "<a href='../../reports/public/data_spike.php?cwms_ts_id=" + responseDatmanData.stage_cwms_ts_id + "&location_id=" + responseDatmanData.location_id + "&latest_time=" + responseMoreDatmanData + "' target='_blank'>" + "Data Spike Stage" + "</a>";
                                                }
                                                
                                                // ElevRev data spike link
                                                resultDatmanDiv.innerHTML += "<br><br>" + "<a href='../../reports/public/data_spike.php?cwms_ts_id=" + responseDatmanData.elev_cwms_ts_id + "&latest_time=" + responseMoreDatmanData + "' target='_blank'>" + "Data Spike Elev" + "</a>"; 
                                            } else {
                                                //resultDatmanDiv.innerHTML = "Error 101";
                                            }
                                        };

                                        // Append the parameter to the URL or modify the request based on your requirements
                                        var urlDatman = "get_latest_time.php?location_id=" + encodeURIComponent(locationIdDatman);

                                        // Make a GET request for the second Ajax call
                                        xhr2.open("GET", urlDatman, true);
                                        xhr2.send();
                                    }
                                
                            </script>


                            <script>
                                // CHANGE PLOT PARAMETER
                                    // Function to make an Ajax request
                                    function getDatmanData(cwms_ts_id_js) {
                                        // Check if the result div is already populated
                                        var resultDatmanDiv = document.getElementById("changePlotParameter");

                                        var xhr = new XMLHttpRequest();

                                        xhr.onreadystatechange = function () {
                                            if (xhr.readyState == 4 && xhr.status == 200) {
                                                // If the request was successful, update the result div
                                                var responseDatmanData = JSON.parse(xhr.responseText);

                                                console.log("responseDatmanData: ", responseDatmanData);

                                                // Access the location_id property (NEED THIS TO CALL METADATA)
                                                var locationIdDatman = responseDatmanData.location_id;
                                                console.log("locationIdDatman: ", locationIdDatman);

                                                var displayStage29Datman = responseDatmanData.display_stage29;
                                                console.log("displayStage29Datman: ", displayStage29Datman);

                                                var stage29CwmstsidDatman = responseDatmanData.stage29_cwms_ts_id;
                                                console.log("stage29CwmstsidDatman: ", stage29CwmstsidDatman);

                                                var stageCwmstsidDatman = responseDatmanData.stage_cwms_ts_id;
                                                console.log("stageCwmstsidDatman: ", stageCwmstsidDatman);

                                                var elevCwmstsidDatman = responseDatmanData.elev_cwms_ts_id;
                                                console.log("elevCwmstsidDatman: ", elevCwmstsidDatman);

                                                // Clear the previous content in the result div
                                                resultDatmanDiv.innerHTML = "";

                                                if (cwms_ts_id_2_dropdown === null) {
                                                    if (displayStage29Datman === "True") {
                                                        var displayOption = "<a href='plot_macro.php?basin=Mississippi&cwms_ts_id=" + stageCwmstsidDatman + "&start_day=4&end_day=0'>" + "View in Stage" + "</a>";
                                                    } else if (parameter_id_rating === "Stage") {
                                                        var displayOption = "<a href='plot_macro.php?basin=Mississippi&cwms_ts_id=" + elevCwmstsidDatman + "&start_day=4&end_day=0'>" + "View in Elevation" + "</a>";
                                                    } else if (parameter_id_rating === "Elev") {
                                                        var displayOption = "<a href='plot_macro.php?basin=Mississippi&cwms_ts_id=" + stageCwmstsidDatman + "&start_day=4&end_day=0'>" + "View in Stage" + "</a>";
                                                    } else {
                                                        var displayOption = "For stage only.";
                                                    }
                                                } else {
                                                    var displayOption = "Disabled for multiple time series plot.";
                                                }

                                                // Update the HTML content of a <div> element with the location_id
                                                resultDatmanDiv.innerHTML = displayOption;

                                            } else {
                                                // Handle the error scenario, e.g., display an error message
                                                resultDatmanDiv.innerHTML = "Loading data. Please wait..." + "<br>" + "<img src='images/loading4.gif' style='height: 30px; width: 30px;'>";
                                            }
                                        };

                                        // Append the parameter to the URL or modify the request based on your requirements
                                        var url = "get_gage_data_page.php?cwms_ts_id_js=" + encodeURIComponent(cwms_ts_id_js);

                                        // Make a GET request to getData.php
                                        xhr.open("GET", url, true);
                                        xhr.send();
                                    }
                                    getDatmanData(cwms_ts_id_js);
                            </script>


                            <script>
                                // QUALITY CODES
                                    // Function to make an Ajax request
                                    function getQualityCodesData(cwms_ts_id_rating) {
                                        // Check if the result div is already populated
                                        var resultQualityCodesDiv = document.getElementById("qualityCodesTable");

                                        var xhr = new XMLHttpRequest();

                                        xhr.onreadystatechange = function () {
                                            if (xhr.readyState == 4 && xhr.status == 200) {
                                                // If the request was successful, update the result div
                                                var responseQualityCodesData = JSON.parse(xhr.responseText);

                                                console.log("responseQualityCodesData: ", responseQualityCodesData);

                                                resultQualityCodesDiv.innerHTML = "";

                                                var table = createQualityCodesTable(responseQualityCodesData);

                                                // Check if any metadata is available
                                                if (responseQualityCodesData !== "" && cwms_ts_id_2_rating === null) {
                                                    resultQualityCodesDiv.appendChild(table);
                                                } else {
                                                    resultQualityCodesDiv.innerHTML = "Disabled for multiple time series plot.";
                                                }
                                            } else {
                                                // Handle the error scenario, e.g., display an error message
                                                resultQualityCodesDiv.innerHTML = "Loading data. Please wait..." + "<br>" + "<img src='images/loading4.gif' style='height: 30px; width: 30px;'>";
                                            }
                                        };

                                        // Append the parameter to the URL or modify the request based on your requirements
                                        var url = "get_quality_codes.php?cwms_ts_id_rating=" + encodeURIComponent(cwms_ts_id_rating);

                                        // Make a GET request to getData.php
                                        xhr.open("GET", url, true);
                                        xhr.send();
                                    }

                                    // Function to create a table from the response data
                                    function createQualityCodesTable(data) {
                                        // Create a table element
                                        var table = document.createElement("table");
                                        table.style.width = "100%"; // Set table width to 100%
                                        table.style.borderCollapse = "collapse"; // Ensure borders collapse

                                        // Create a header row
                                        var headerRow = table.insertRow(0);
                                        for (var key in data[0]) {
                                            var th = document.createElement("th");
                                            th.innerHTML = key;
                                            th.style.border = "1px solid #ccc";
                                            headerRow.appendChild(th);
                                        }

                                        // Add data rows
                                        for (var i = 0; i < data.length; i++) {
                                            var row = table.insertRow(i + 1);
                                            for (var key in data[i]) {
                                                var cell = row.insertCell();

                                                // If the current cell corresponds to the 'quality_code' property, add a dynamic link
                                                if (key === 'quality_code') {
                                                    var link = document.createElement("a");
                                                    link.href = "../../reports/public/quality_code.php?cwms_ts_id=" + cwms_ts_id_rating + "&quality_code=" + data[i][key] + "&unit_id=" + unit_id_rating;
                                                    link.target = "_blank";
                                                    link.innerHTML = data[i][key];
                                                    cell.appendChild(link);
                                                } else {
                                                    cell.innerHTML = data[i][key];
                                                }

                                                cell.style.border = "1px solid #ccc";
                                                cell.style.paddingLeft = "8px";
                                            }
                                        }

                                        return table;
                                    }


                                    getQualityCodesData(cwms_ts_id_rating);
                            </script>


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

                            // Time Series 2
                                var cwms_ts_id_2 = <?php echo json_encode($cwms_ts_id_2); ?>;
                                var location_id_2 = <?php echo json_encode($location_id_2); ?>;
                                var parameter_id_2 = <?php echo json_encode($parameter_id_2); ?>;
                                var date_time_2 = <?php echo json_encode($date_time_2); ?>;
                                var value_2 = <?php echo json_encode($value_2); ?>;
                                var unit_2 = <?php echo json_encode($unit_2); ?>;
                                var quality_code_2 = <?php echo json_encode($quality_code_2); ?>;
                                console.log("cwms_ts_id_2 = ", cwms_ts_id_2);
                                console.log("location_id_2 = ", location_id_2);
                                console.log("parameter_id_2 = ", parameter_id_2);
                                console.log("date_time_2 = ", date_time_2);
                                console.log("value_2 = ", value_2);
                                console.log("unit_2 = ", unit_2);
                                console.log("quality_code_2 = ", quality_code_2);

                                let firstCwmsTsId_2 = cwms_ts_id_2[0];
                                console.log("firstCwmsTsId_2 = ",firstCwmsTsId_2);

                                let firstLocationId_2 = location_id_2[0];
                                console.log("firstLocationId_2 = ",firstLocationId_2);

                                let firstParameterId_2 = parameter_id_2[0];
                                console.log("firstParameterId_2 = ",firstParameterId_2);

                                const firstValueUnit_2 = unit_2[0];
                                console.log("firstValueUnit_2 = ",firstValueUnit_2);

                            // Time Series 3
                                var cwms_ts_id_3 = <?php echo json_encode($cwms_ts_id_3); ?>;
                                var location_id_3 = <?php echo json_encode($location_id_3); ?>;
                                var parameter_id_3 = <?php echo json_encode($parameter_id_3); ?>;
                                var date_time_3 = <?php echo json_encode($date_time_3); ?>;
                                var value_3 = <?php echo json_encode($value_3); ?>;
                                var unit_3 = <?php echo json_encode($unit_3); ?>;
                                var quality_code_3 = <?php echo json_encode($quality_code_3); ?>;
                                console.log("cwms_ts_id_3 = ", cwms_ts_id_3);
                                console.log("location_id_3 = ", location_id_3);
                                console.log("parameter_id_3 = ", parameter_id_3);
                                console.log("date_time_3 = ", date_time_3);
                                console.log("value_3 = ", value_3);
                                console.log("unit_3 = ", unit_3);
                                console.log("quality_code_3 = ", quality_code_3);

                                let firstCwmsTsId_3 = cwms_ts_id_3[0];
                                console.log("firstCwmsTsId_3 = ",firstCwmsTsId_3);

                                let firstLocationId_3 = location_id_3[0];
                                console.log("firstLocationId_3 = ",firstLocationId_3);

                                let firstParameterId_3 = parameter_id_3[0];
                                console.log("firstParameterId_3 = ",firstParameterId_3);

                                const firstValueUnit_3 = unit_3[0];
                                console.log("firstValueUnit_3 = ",firstValueUnit_3);

                            // Time Series 4
                                var cwms_ts_id_4 = <?php echo json_encode($cwms_ts_id_4); ?>;
                                var location_id_4 = <?php echo json_encode($location_id_4); ?>;
                                var parameter_id_4 = <?php echo json_encode($parameter_id_4); ?>;
                                var date_time_4 = <?php echo json_encode($date_time_4); ?>;
                                var value_4 = <?php echo json_encode($value_4); ?>;
                                var unit_4 = <?php echo json_encode($unit_4); ?>;
                                var quality_code_4 = <?php echo json_encode($quality_code_4); ?>;
                                console.log("cwms_ts_id_4 = ", cwms_ts_id_4);
                                console.log("location_id_4 = ", location_id_4);
                                console.log("parameter_id_4 = ", parameter_id_4);
                                console.log("date_time_4 = ", date_time_4);
                                console.log("value_4 = ", value_4);
                                console.log("unit_4 = ", unit_4);
                                console.log("quality_code_4 = ", quality_code_4);

                                let firstCwmsTsId_4 = cwms_ts_id_4[0];
                                console.log("firstCwmsTsId_4 = ",firstCwmsTsId_4);

                                let firstLocationId_4 = location_id_4[0];
                                console.log("firstLocationId_4 = ",firstLocationId_4);

                                let firstParameterId_4 = parameter_id_4[0];
                                console.log("firstParameterId_4 = ",firstParameterId_4);

                                const firstValueUnit_4 = unit_4[0];
                                console.log("firstValueUnit_4 = ",firstValueUnit_4);

                            // Time Series 5
                                var cwms_ts_id_5 = <?php echo json_encode($cwms_ts_id_5); ?>;
                                var location_id_5 = <?php echo json_encode($location_id_5); ?>;
                                var parameter_id_5 = <?php echo json_encode($parameter_id_5); ?>;
                                var date_time_5 = <?php echo json_encode($date_time_5); ?>;
                                var value_5 = <?php echo json_encode($value_5); ?>;
                                var unit_5 = <?php echo json_encode($unit_5); ?>;
                                var quality_code_5 = <?php echo json_encode($quality_code_5); ?>;
                                console.log("cwms_ts_id_5 = ", cwms_ts_id_5);
                                console.log("location_id_5 = ", location_id_5);
                                console.log("parameter_id_5 = ", parameter_id_5);
                                console.log("date_time_5 = ", date_time_5);
                                console.log("value_5 = ", value_5);
                                console.log("unit_5 = ", unit_5);
                                console.log("quality_code_5 = ", quality_code_5);

                                let firstCwmsTsId_5 = cwms_ts_id_5[0];
                                console.log("firstCwmsTsId_5 = ",firstCwmsTsId_5);

                                let firstLocationId_5 = location_id_5[0];
                                console.log("firstLocationId_5 = ",firstLocationId_5);

                                let firstParameterId_5 = parameter_id_5[0];
                                console.log("firstParameterId_5 = ",firstParameterId_5);

                                const firstValueUnit_5 = unit_5[0];
                                console.log("firstValueUnit_5 = ",firstValueUnit_5);

                            // Time Series 6
                                var cwms_ts_id_6 = <?php echo json_encode($cwms_ts_id_6); ?>;
                                var location_id_6 = <?php echo json_encode($location_id_6); ?>;
                                var parameter_id_6 = <?php echo json_encode($parameter_id_6); ?>;
                                var date_time_6 = <?php echo json_encode($date_time_6); ?>;
                                var value_6 = <?php echo json_encode($value_6); ?>;
                                var unit_6 = <?php echo json_encode($unit_6); ?>;
                                var quality_code_6 = <?php echo json_encode($quality_code_6); ?>;
                                console.log("cwms_ts_id_6 = ", cwms_ts_id_6);
                                console.log("location_id_6 = ", location_id_6);
                                console.log("parameter_id_6 = ", parameter_id_6);
                                console.log("date_time_6 = ", date_time_6);
                                console.log("value_6 = ", value_6);
                                console.log("unit_6 = ", unit_6);
                                console.log("quality_code_6 = ", quality_code_6);

                                let firstCwmsTsId_6 = cwms_ts_id_6[0];
                                console.log("firstCwmsTsId_6 = ",firstCwmsTsId_6);

                                let firstLocationId_6 = location_id_6[0];
                                console.log("firstLocationId_6 = ",firstLocationId_6);

                                let firstParameterId_6 = parameter_id_6[0];
                                console.log("firstParameterId_6 = ",firstParameterId_6);

                                const firstValueUnit_6 = unit_6[0];
                                console.log("firstValueUnit_6 = ",firstValueUnit_6);

                            // Time Series 7
                                var cwms_ts_id_7 = <?php echo json_encode($cwms_ts_id_7); ?>;
                                var location_id_7 = <?php echo json_encode($location_id_7); ?>;
                                var parameter_id_7 = <?php echo json_encode($parameter_id_7); ?>;
                                var date_time_7 = <?php echo json_encode($date_time_7); ?>;
                                var value_7 = <?php echo json_encode($value_7); ?>;
                                var unit_7 = <?php echo json_encode($unit_7); ?>;
                                var quality_code_7 = <?php echo json_encode($quality_code_7); ?>;
                                console.log("cwms_ts_id_7 = ", cwms_ts_id_7);
                                console.log("location_id_7 = ", location_id_7);
                                console.log("parameter_id_7 = ", parameter_id_7);
                                console.log("date_time_7 = ", date_time_7);
                                console.log("value_7 = ", value_7);
                                console.log("unit_7 = ", unit_7);
                                console.log("quality_code_7 = ", quality_code_7);

                                let firstCwmsTsId_7 = cwms_ts_id_7[0];
                                console.log("firstCwmsTsId_7 = ",firstCwmsTsId_7);

                                let firstLocationId_7 = location_id_7[0];
                                console.log("firstLocationId_7 = ",firstLocationId_7);

                                let firstParameterId_7 = parameter_id_7[0];
                                console.log("firstParameterId_7 = ",firstParameterId_7);

                                const firstValueUnit_7 = unit_7[0];
                                console.log("firstValueUnit_7 = ",firstValueUnit_7);


                            // Time Series 8 FLOOD LEVEL
                                var constant_level_time_series_flood = <?php echo json_encode($constant_level_time_series_flood); ?>;
                                console.log("constant_level_time_series_flood = ", constant_level_time_series_flood);

                                var date_time_8 = <?php echo json_encode($date_time_array_flood); ?>;
                                var value_8 = <?php echo json_encode($constant_value_array_flood); ?>;
                                console.log("date_time_8 = ", date_time_8);
                                console.log("value_8 = ", value_8);

                                var allFloodValuesLessThan900 = true;

                                for (var i = 0; i < value_8.length; i++) {
                                    if (value_8[i] >= 900) {
                                        allFloodValuesLessThan900 = false;
                                        break;  // Exit the loop early since we found a value >= 900
                                    }
                                }

                                console.log("allFloodValuesLessThan900 = ", allFloodValuesLessThan900);


                            // Time Series 9 LWRP
                                var constant_level_time_series_lwrp = <?php echo json_encode($constant_level_time_series_lwrp); ?>;
                                console.log("constant_level_time_series_lwrp = ", constant_level_time_series_lwrp);

                                var date_time_9 = <?php echo json_encode($date_time_array_lwrp); ?>;
                                var value_9 = <?php echo json_encode($constant_value_array_lwrp); ?>;
                                console.log("date_time_9 = ", date_time_9);
                                console.log("value_9 = ", value_9);

                                var allLWRPValuesLessThan900 = true;

                                for (var i = 0; i < value_9.length; i++) {
                                    if (value_9[i] >= 900) {
                                        allLWRPValuesLessThan900 = false;
                                        break;  // Exit the loop early since we found a value >= 900
                                    }
                                }

                                console.log("allLWRPValuesLessThan900 = ", allLWRPValuesLessThan900);

                            // Time Series 10 Hinge Max
                                var constant_level_time_series_hinge_max = <?php echo json_encode($constant_level_time_series_hinge_max); ?>;
                                    console.log("constant_level_time_series_hinge_max = ", constant_level_time_series_hinge_max);

                                    var date_time_10 = <?php echo json_encode($date_time_array_hinge_max); ?>;
                                    var value_10 = <?php echo json_encode($constant_value_array_hinge_max); ?>;
                                    console.log("date_time_10 = ", date_time_10);
                                    console.log("value_10 = ", value_10);

                                    var allHingeMaxValuesLessThan900 = true;

                                    for (var i = 0; i < value_10.length; i++) {
                                        if (value_10[i] >= 900) {
                                            allHingeMaxValuesLessThan900 = false;
                                            break;  // Exit the loop early since we found a value >= 900
                                        }
                                    }

                                    console.log("allHingeMaxValuesLessThan900 = ", allHingeMaxValuesLessThan900);


                            // Time Series 11 Hinge Min
                                var constant_level_time_series_hinge_min = <?php echo json_encode($constant_level_time_series_hinge_min); ?>;
                                    console.log("constant_level_time_series_hinge_min = ", constant_level_time_series_hinge_min);

                                    var date_time_11 = <?php echo json_encode($date_time_array_hinge_min); ?>;
                                    var value_11 = <?php echo json_encode($constant_value_array_hinge_min); ?>;
                                    console.log("date_time_11 = ", date_time_11);
                                    console.log("value_11 = ", value_11);

                                    var allHingeMinValuesLessThan900 = true;

                                    for (var i = 0; i < value_11.length; i++) {
                                        if (value_11[i] >= 900) {
                                            allHingeMinValuesLessThan900 = false;
                                            break;  // Exit the loop early since we found a value >= 900
                                        }
                                    }

                                    console.log("allHingeMinValuesLessThan900 = ", allHingeMinValuesLessThan900);

                            // Function: check similar variables
                                function areVariablesEqual() {
                                    var definedValue = undefined;
                                    for (var i = 0; i < arguments.length; i++) {
                                        if (typeof arguments[i] !== 'undefined') {
                                            if (definedValue === undefined) {
                                                definedValue = arguments[i];
                                            } else if (arguments[i] !== definedValue) {
                                                return false;
                                            }
                                        }
                                    }
                                    return true;
                                }

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

                                let roundedMinValueAxis_y2;
                                let roundedMaxValueAxis_y2;

                                if (areVariablesEqual(firstParameterId, firstParameterId_2, firstParameterId_3, firstParameterId_4, firstParameterId_5, firstParameterId_6, firstParameterId_7)) {
                                    console.log("All firstParameterId are the same.");
                                    // time series 1
                                    var filteredValues = value.filter(value => value !== null); // Use filter to remove null values
                                    var minValue = Math.min(...filteredValues);
                                    var maxValue = Math.max(...filteredValues);
                                    console.log("minValue = ", minValue);
                                    console.log("maxValue = ", maxValue);
                                    // time series 2
                                    var minValue_2 = Math.min(...value_2);
                                    var maxValue_2 = Math.max(...value_2);
                                    console.log("minValue_2 = ", minValue_2);
                                    console.log("maxValue_2 = ", maxValue_2);
                                    // time series 3
                                    var minValue_3 = Math.min(...value_3);
                                    var maxValue_3 = Math.max(...value_3);
                                    console.log("minValue_3 = ", minValue_3);
                                    console.log("maxValue_3 = ", maxValue_3);
                                    // time series 4
                                    var minValue_4 = Math.min(...value_4);
                                    var maxValue_4 = Math.max(...value_4);
                                    console.log("minValue_4 = ", minValue_4);
                                    console.log("maxValue_4 = ", maxValue_4);
                                    // time series 5
                                    var minValue_5 = Math.min(...value_5);
                                    var maxValue_5 = Math.max(...value_5);
                                    console.log("minValue_5 = ", minValue_5);
                                    console.log("maxValue_5 = ", maxValue_5);
                                    // time series 6
                                    var minValue_6 = Math.min(...value_6);
                                    var maxValue_6 = Math.max(...value_6);
                                    console.log("minValue_6 = ", minValue_6);
                                    console.log("maxValue_6 = ", maxValue_6);
                                    // time series 7
                                    var minValue_7 = Math.min(...value_7);
                                    var maxValue_7 = Math.max(...value_7);
                                    console.log("minValue_7 = ", minValue_7);
                                    console.log("maxValue_7 = ", maxValue_7);

                                    // find the min from 7 time series array
                                    const minNumber_y1 = Math.min(minValue, minValue_2, minValue_3, minValue_4, minValue_5, minValue_6, minValue_7);
                                    console.log("minNumber_y1 = ", minNumber_y1);
                                    // find the max from 7 time series array
                                    const maxNumber_y1 = Math.max(maxValue, maxValue_2, maxValue_3, maxValue_4, maxValue_5, maxValue_6, maxValue_7);
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
                                } else {
                                    console.log("Some firstParameterId are different.");
                                    // find the minimum and maximum values in each time series array
                                    // time series 1
                                    var filteredValues = value.filter(value => value !== null); // Use filter to remove null values
                                    var minValue = Math.min(...filteredValues);
                                    var maxValue = Math.max(...filteredValues);
                                    console.log("minValue = ", minValue);
                                    console.log("maxValue = ", maxValue);
                                    // time series 2
                                    var minValue_2 = Math.min(...value_2);
                                    var maxValue_2 = Math.max(...value_2);
                                    console.log("minValue_2 = ", minValue_2);
                                    console.log("maxValue_2 = ", maxValue_2);
                                    // time series 3
                                    var minValue_3 = Math.min(...value_3);
                                    var maxValue_3 = Math.max(...value_3);
                                    console.log("minValue_3 = ", minValue_3);
                                    console.log("maxValue_3 = ", maxValue_3);
                                    // time series 4
                                    var minValue_4 = Math.min(...value_4);
                                    var maxValue_4 = Math.max(...value_4);
                                    console.log("minValue_4 = ", minValue_4);
                                    console.log("maxValue_4 = ", maxValue_4);
                                    // time series 5
                                    var minValue_5 = Math.min(...value_5);
                                    var maxValue_5 = Math.max(...value_5);
                                    console.log("minValue_5 = ", minValue_5);
                                    console.log("maxValue_5 = ", maxValue_5);
                                    // time series 6
                                    var minValue_6 = Math.min(...value_6);
                                    var maxValue_6 = Math.max(...value_6);
                                    console.log("minValue_6 = ", minValue_6);
                                    console.log("maxValue_6 = ", maxValue_6);
                                    // time series 7
                                    var minValue_7 = Math.min(...value_7);
                                    var maxValue_7 = Math.max(...value_7);
                                    console.log("minValue_7 = ", minValue_7);
                                    console.log("maxValue_7 = ", maxValue_7);
                                    // find the min max y1
                                    let minNumber_y1 = Math.min(minValue, minValue_3, minValue_5, minValue_7);
                                    console.log("minNumber_y1 = ", minNumber_y1);
                                    let maxNumber_y1 = Math.max(maxValue, maxValue_3, maxValue_5, maxValue_7);
                                    console.log("maxNumber_y1 = ", maxNumber_y1);
                                    // find the min max y2
                                    let minNumber_y2 = Math.min(minValue_2, minValue_4, minValue_6);
                                    console.log("minNumber_y2 = ", minNumber_y2);
                                    let maxNumber_y2 = Math.max(maxValue_2, maxValue_4, maxValue_6);
                                    console.log("maxNumber_y2 = ", maxNumber_y2);

                                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// FOR Y1
                                    if (firstParameterId == "Stage" || firstParameterId == "Elev") {
                                        console.log("find min and max value for axis for Stage or Elev");
                                        //////////////////////////////////////////////////// find min value for axis for STAGE
                                        if (minNumber_y1<=0) { 
                                            var minValueAxis_y1 = minNumber_y1 - 1;
                                            console.log("less than 0");
                                        } else if (minNumber_y1<=1) {
                                            var minValueAxis_y1 = minNumber_y1 - 1;
                                            console.log("less than 1");
                                        } else if (minNumber_y1>200) {
                                            console.log("greater than 200");
                                            var minValueAxis_y1 = minNumber_y1 -1;
                                        } else {
                                            var minValueAxis_y1 = minNumber_y1 - (minNumber_y1*0.1);
                                            console.log("minus 10%");
                                        }
                                        console.log("minValueAxis_y1 = ", minValueAxis_y1);
                                        //////////////////////////////////////////////////// find max value for axis for STAGE
                                        if (maxNumber_y1<=0) {
                                            console.log("less than 0");
                                            var maxValueAxis_y1 = maxNumber_y1 + 1;
                                        } else if (maxNumber_y1<=1) {
                                            console.log("less than 1");
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
                                        //////////////////////////////////////////////////// find min value for axis for FLOW
                                        if (minNumber_y1 <= 0) {
                                            var minValueAxis_y1 = Math.round(minNumber_y1) - 2;
                                            console.log("flow is less and equal to 0");
                                        } else if (minNumber_y1 > 0 && minNumber_y1 <= 50) {
                                            var minValueAxis_y1 = Math.round(minNumber_y1) - 2;
                                            console.log("flow is greater than 0 and less than 50");
                                        } else if (minNumber_y1 > 50000) {
                                            var minValueAxis_y1 = (Math.round(minNumber_y1/1000)*1000) - 5000;
                                            console.log("flow is greater than 50,000");
                                        } else if (minNumber_y1 > 100000){
                                            var minValueAxis_y1 = (Math.round(minNumber_y1/1000)*1000) - 10000;
                                            console.log("flow is greater than 100,000");
                                        } else {
                                            var minValueAxis_y1 = minNumber_y1 - ((minNumber_y1*0.1));
                                            console.log("flow is minus 10%");
                                        }
                                        console.log("minValueAxis_y1 = ", minValueAxis_y1);
                                        //////////////////////////////////////////////////// find max value for axis for FLOW
                                        if (maxNumber_y1 > 0 && maxNumber_y1 <= 50) {
                                            var maxValueAxis_y1 = Math.round(maxNumber_y1) + 2;
                                            console.log("flow is greater and equal to 0 and less than 50");
                                        } else if (maxNumber_y1 > 50000) {
                                            var maxValueAxis_y1 = (Math.round(maxNumber_y1/1000)*1000) + 5000;
                                            console.log("flow is greater than 50,000");
                                        } else if (maxNumber_y1 > 100000) {
                                            var maxValueAxis_y1 = (Math.round(maxNumber_y1/1000)*1000) + 10000;
                                            console.log("flow is greater than 100,000");
                                        } else {
                                            var maxValueAxis_y1 = maxNumber_y1 + ((maxNumber_y1*0.1)+1);
                                            console.log("flow is plus 10%");
                                        }
                                        console.log("maxValueAxis_y1 = ", maxValueAxis_y1);
                                    } else {
                                        console.log("find min and max value for axis for everything else");
                                        //////////////////////////////////////////////////// find min value for axis for everything else
                                        var minValueAxis_y1 = minNumber_y1 - ((minNumber_y1*0.1));
                                        console.log("minValueAxis_y1 = ", minValueAxis_y1);
                                        //////////////////////////////////////////////////// find max value for axis for everything else
                                        var maxValueAxis_y1 = maxNumber_y1 + ((maxNumber_y1*0.1)+1);
                                        console.log("maxValueAxis_y1 = ", maxValueAxis_y1);
                                    }
                                    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////// FOR Y2
                                    if (firstParameterId == "Stage" || firstParameterId == "Elev") {
                                        console.log("find min and max value for axis for Stage or Elev");
                                        //////////////////////////////////////////////////// find min value for axis for STAGE
                                        if (minNumber_y2<=0) { 
                                            var minValueAxis_y2 = minNumber_y2 - 1;
                                            console.log("less than 0");
                                        } else if (minNumber_y2<=1) {
                                            var minValueAxis_y2 = minNumber_y2 - 1;
                                            console.log("less than 1");
                                        } else if (minNumber_y2>200) {
                                            console.log("greater than 200");
                                            var minValueAxis_y2 = minNumber_y2 - 1;
                                        } else {
                                            var minValueAxis_y2 = minNumber_y2 - (minNumber_y2*0.1);
                                            console.log("minus 10%");
                                        }
                                        console.log("minValueAxis_y2 = ", minValueAxis_y2);
                                        //////////////////////////////////////////////////// find max value for axis for STAGE
                                        if (maxNumber_y2<=0) {
                                            console.log("less than 0");
                                            var maxValueAxis_y2 = maxNumber_y2 + 1;
                                        } else if (maxNumber_y2<=1) {
                                            console.log("less than 1");
                                            var maxValueAxis_y2 = maxNumber_y2 + 1;
                                        } else if (maxNumber_y2 > 200) {
                                            console.log("greater than 200");
                                            var maxValueAxis_y2 = maxNumber_y2 + 1;
                                        } else {
                                            var maxValueAxis_y2 = maxNumber_y2 + (maxNumber_y2*0.1);
                                            console.log("plus 10%");
                                        }
                                        console.log("maxValueAxis_y2 = ", maxValueAxis_y2);
                                    } else if (firstParameterId == "Flow") {
                                        console.log("find min and max value for axis for Flow");
                                        //////////////////////////////////////////////////// find min value for axis for FLOW
                                        if (minNumber_y2 <= 0) {
                                            var minValueAxis_y2 = Math.round(minNumber_y2) - 5;
                                            console.log("less and equal to 0");
                                        } else if (minNumber_y2 > 0 && minNumber_y2 <= 50) {
                                            var minValueAxis_y2 = Math.round(minNumber_y2) - 5;
                                            console.log("greater than 0 and less than 50");
                                        } else if (minNumber_y2 > 50000) {
                                            var minValueAxis_y2 = (Math.round(minNumber_y2/1000)*1000) - 5000;
                                            console.log("greater than 50,000");
                                        } else if (minNumber_y2 > 100000){
                                            var minValueAxis_y2 = (Math.round(minNumber_y2/1000)*1000) - 10000;
                                            console.log("greater than 100,000");
                                        } else {
                                            var minValueAxis_y2 = minNumber_y2 - (minNumber_y2*0.1);
                                            console.log("minus 10%");
                                        }
                                        console.log("minValueAxis_y2 = ", minValueAxis_y2);
                                        //////////////////////////////////////////////////// find max value for axis for FLOW
                                        if (maxNumber_y2 > 0 && maxNumber_y2 <= 50) {
                                            var maxValueAxis_y2 = Math.round(maxNumber_y2) + 5;
                                            console.log("greater and equal to 0 and less than 50");
                                        } else if (maxNumber_y2 > 50000) {
                                            var maxValueAxis_y2 = (Math.round(maxNumber_y2/1000)*1000) + 5000;
                                            console.log("greater than 50,000");
                                        } else if (maxNumber_y2 > 100000) {
                                            var maxValueAxis_y2 = (Math.round(maxNumber_y2/1000)*1000) + 10000;
                                            console.log("greater than 100,000");
                                        } else {
                                            var maxValueAxis_y2 = maxNumber_y2 + (maxNumber_y2*0.1);
                                            console.log("plus 10%");
                                        }
                                        console.log("maxValueAxis_y2 = ", maxValueAxis_y2);
                                    } else {
                                        console.log("find min and max value for axis for everything else");
                                        //////////////////////////////////////////////////// find min value for axis for everything else
                                        var minValueAxis_y2 = minNumber_y2 - (minNumber_y2*0.1);
                                        console.log("minValueAxis_y2 = ", minValueAxis_y2);
                                        //////////////////////////////////////////////////// find max value for axis for everything else
                                        var maxValueAxis_y2 = maxNumber_y2 + (maxNumber_y2*0.1);
                                        console.log("maxValueAxis_y2 = ", maxValueAxis_y2);
                                    }
                                    //////////////////////////////////////////////////// rounded min max value for axis
                                    roundedMinValueAxis_y1 = Math.round(minValueAxis_y1);
                                    console.log("roundedMinValueAxis_y1 = ", roundedMinValueAxis_y1);
                                    roundedMaxValueAxis_y1 = Math.round(maxValueAxis_y1);
                                    console.log("roundedMaxValueAxis_y1 = ", roundedMaxValueAxis_y1);
                                    //////////////////////////////////////////////////// rounded min max value for axis
                                    roundedMinValueAxis_y2 = Math.round(minValueAxis_y2);
                                    console.log("roundedMinValueAxis_y2 = ", roundedMinValueAxis_y2);
                                    roundedMaxValueAxis_y2 = Math.round(maxValueAxis_y2);
                                    console.log("roundedMaxValueAxis_y2 = ", roundedMaxValueAxis_y2);
                                }

                                console.log("after find min max loop");
                                console.log("roundedMinValueAxis_y1 = ", roundedMinValueAxis_y1);
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

                                // time series 2
                                const data_2 = date_time_2.map((element, index) => [element, value_2[index], unit_2[index], quality_code[index]]);
                                console.log("combined " + firstLocationId_2 + " = ", data_2);

                                // time series 3
                                const data_3 = date_time_3.map((element, index) => [element, value_3[index], unit_3[index], quality_code[index]]);
                                console.log("combined " + firstLocationId_3 + " = ", data_3);

                                // time series 4
                                const data_4 = date_time_4.map((element, index) => [element, value_4[index], unit_4[index], quality_code[index]]);
                                console.log("combined " + firstLocationId_4 + " = ", data_4);

                                // time series 5
                                const data_5 = date_time_5.map((element, index) => [element, value_5[index], unit_5[index], quality_code[index]]);
                                console.log("combined " + firstLocationId_5 + " = ", data_5);

                                // time series 6
                                const data_6 = date_time_6.map((element, index) => [element, value_6[index], unit_6[index], quality_code[index]]);
                                console.log("combined " + firstLocationId_6 + " = ", data_6);

                                // time series 7
                                const data_7 = date_time_7.map((element, index) => [element, value_7[index], unit_7[index], quality_code[index]]);
                                console.log("combined " + firstLocationId_7 + " = ", data_7);



                            // Initial visibility state of datasets
                                var dataset1Visible = true;
                                var dataset2Visible = true;
                                var dataset3Visible = true;
                                var dataset4Visible = true;
                                var dataset5Visible = true;
                                var dataset6Visible = true;
                                var dataset7Visible = true;
                                var dataset8Visible = false; // flood level
                                var dataset9Visible = false; // lwrp
                                var dataset10Visible = false; // hinge max
                                var dataset11Visible = false; // hinge min

                            // Create a Chart.js Chart
                                var ctx = document.getElementById('lineChart').getContext('2d');
                                // Assuming date_time and date_time_2 are the timestamps for both datasets, and value and value_2 are the corresponding data values
                                // Align data points based on timestamps, Create an array of aligned data points based on the dataset with the most data points
                                const alignedData = [];

                                console.log("looping through datetime to alignedData");
                                for (let i = 0; i < date_time.length; i++) {
                                    const timestamp = date_time[i];
                                    //console.log("timestamp = ", timestamp);

                                    // Find the corresponding index in date_time_2
                                    const indexInData2 = date_time_2.indexOf(timestamp);
                                    //console.log("indexInData2 = ", indexInData2);

                                    // Find the corresponding index in date_time_3
                                    const indexInData3 = date_time_3.indexOf(timestamp);
                                    //console.log("indexInData3 = ", indexInData3);

                                    // Find the corresponding index in date_time_4
                                    const indexInData4 = date_time_4.indexOf(timestamp);
                                    //console.log("indexInData4 = ", indexInData4);

                                    // Find the corresponding index in date_time_5
                                    const indexInData5 = date_time_5.indexOf(timestamp);
                                    //console.log("indexInData5 = ", indexInData5);

                                    // Find the corresponding index in date_time_6
                                    const indexInData6 = date_time_6.indexOf(timestamp);
                                    //console.log("indexInData6 = ", indexInData6);

                                    // Find the corresponding index in date_time_7
                                    const indexInData7 = date_time_7.indexOf(timestamp);
                                    //console.log("indexInData7 = ", indexInData7);

                                    const alignedValue = {
                                        x: new Date(timestamp),
                                        y1: value[i],                  							// Value from the first dataset
                                        y2: indexInData2 !== -1 ? value_2[indexInData2] : null, // Corresponding value from the second dataset
                                        y3: indexInData3 !== -1 ? value_3[indexInData3] : null, // Corresponding value from the third dataset
                                        y4: indexInData4 !== -1 ? value_4[indexInData4] : null, // Corresponding value from the fourth dataset
                                        y5: indexInData5 !== -1 ? value_5[indexInData5] : null, // Corresponding value from the fifth dataset
                                        y6: indexInData6 !== -1 ? value_6[indexInData6] : null, // Corresponding value from the sixth dataset
                                        y7: indexInData7 !== -1 ? value_7[indexInData7] : null  // Corresponding value from the seventh dataset
                                    };
                                    alignedData.push(alignedValue);
                                }

                                console.log("alignedData = ", alignedData);

                                var dataset1 = null; // Default to null
                                var dataset2 = null; // Default to null
                                var dataset3 = null; // Default to null
                                var dataset4 = null; // Default to null
                                var dataset5 = null; // Default to null
                                var dataset6 = null; // Default to null
                                var dataset7 = null; // Default to null

                                if (firstCwmsTsId_2) {
                                    console.log("xxx");
                                }

                            // Dataset 1
                                if (value.length > 0 && firstCwmsTsId_2 == null) {
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
                                        //default (default): This is the default cubic interpolation mode. It uses a single cubic Bezier curve to connect data points.
                                        //monotone: This mode creates a single cubic Bezier curve that is guaranteed to be monotone (non-increasing or non-decreasing) between data points. This can be useful when dealing with data that has natural trends.
                                        //stepped: This mode connects data points with horizontal and vertical lines, creating a stepped appearance. It doesn't use curves and is suitable for step-like data.
                                        pointRadius: 0.2, // Set pointRadius to 0 to hide data point dots
                                        showLine: true, // Show the connecting line
                                        //pointBackgroundColor: 'rgba(0, 0, 255, 1)', // Data point dot color (blue in this example)
                                        //hoverBackgroundColor: 'rgba(0, 0, 255, 1)', // blue hoverBackgroundColor and hoverBorderColor: These parameters let you define the background and border colors when a user hovers over a chart element.
                                        //hoverBorderColor: 'rgba(0, 255, 0, 1)', // green 
                                        //hoverBorderWidth: 1,//Controls the border width when hovering over a chart element.
                                        hidden: !dataset1Visible, // Initially visible or hidden based on flag	
                                    };
                                } else {
                                    console.log("value.length = ", value.length);
                                    dataset1 = {
                                        label: firstCwmsTsId,
                                        data: alignedData.map(entry => ({ x: entry.x, y: entry.y1 })),
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

                            // Dataset 2
                                if (value_2.length > 0 && (firstParameterId==firstParameterId_2)) { // use y1 if first two parameter id are the same
                                    console.log("value_2.length = ", value_2.length);
                                    console.log("Same ParameterId in dataset2");
                                    dataset2 = {
                                        label: firstCwmsTsId_2,
                                        data: alignedData.map(entry => ({ x: entry.x, y: entry.y2 })), // Map y2 values from alignedData
                                        yAxisID: 'y',
                                        borderColor: 'rgba(0, 0, 255, 1)', // blue
                                        backgroundColor: 'rgba(0, 0, 255, 1)', // blue
                                        borderWidth: 2,
                                        fill: false,
                                        tension: 1.0,
                                        cubicInterpolationMode: 'default',
                                        pointRadius: 0.2,
                                        showLine: true,
                                        hidden: !dataset1Visible,
                                    };
                                } else if (value_2.length > 0) {
                                    console.log("value_2.length = ", value_2.length);
                                    dataset2 = {
                                        label: firstCwmsTsId_2,
                                        data: alignedData.map(entry => ({ x: entry.x, y: entry.y2 })), // Map y2 values from alignedData
                                        yAxisID: 'y2', // Assign to the first y-axis
                                        borderColor: 'rgba(0, 0, 255, 1)',
                                        backgroundColor: 'rgba(0, 0, 255, 1)',
                                        borderWidth: 2,
                                        fill: false,
                                        tension: 1.0,
                                        cubicInterpolationMode: 'default',
                                        pointRadius: 0.2,
                                        showLine: true,
                                        hidden: !dataset1Visible,
                                    };
                                } else {

                                }

                            // Dataset 3
                                if (value_3.length > 0) {
                                    console.log("value_3.length = ", value_3.length);
                                    dataset3 = {
                                        label: firstCwmsTsId_3,
                                        data: alignedData.map(entry => ({ x: entry.x, y: entry.y3 })),
                                        yAxisID: 'y',
                                        borderColor: 'rgba(0, 255, 0, 1)', // green
                                        backgroundColor: 'rgba(0, 255, 0, 1)', // green
                                        borderWidth: 2,
                                        fill: false,
                                        tension: 1.0,
                                        cubicInterpolationMode: 'default',
                                        pointRadius: 0.2,
                                        showLine: true,
                                        hidden: !dataset1Visible,
                                    };
                                } else {

                                }

                            // Dataset 4
                                if (value_4.length > 0 && (firstParameterId==firstParameterId_2)) {
                                    console.log("value_4.length = ", value_4.length);
                                    console.log("Same Parameter dataset4");
                                    dataset4 = {
                                        label: firstCwmsTsId_4,
                                        data: alignedData.map(entry => ({ x: entry.x, y: entry.y4 })),
                                        yAxisID: 'y',
                                        borderColor: 'rgba(128, 0, 128, 1)', // purple
                                        backgroundColor: 'rgba(128, 0, 128, 1)', // purple
                                        borderWidth: 2,
                                        fill: false,
                                        tension: 1.0,
                                        cubicInterpolationMode: 'default',
                                        pointRadius: 0.2,
                                        showLine: true,
                                        hidden: !dataset1Visible,
                                    };	
                                } else if (value_4.length > 0) {
                                    console.log("value_4.length = ", value_4.length);
                                    dataset4 = {
                                        label: firstCwmsTsId_4,
                                        data: alignedData.map(entry => ({ x: entry.x, y: entry.y4 })),
                                        yAxisID: 'y2',
                                        borderColor: 'rgba(128, 0, 128, 1)', // purple
                                        backgroundColor: 'rgba(128, 0, 128, 1)', // purple
                                        borderWidth: 2,
                                        fill: false,
                                        tension: 1.0,
                                        cubicInterpolationMode: 'default',
                                        pointRadius: 0.2,
                                        showLine: true,
                                        hidden: !dataset1Visible,
                                    };
                                } else {

                                }


                            // Dataset 5
                                if (value_5.length > 0) {
                                    console.log("value_5.length = ", value_5.length);
                                    dataset5 = {
                                        label: firstCwmsTsId_5,
                                        data: alignedData.map(entry => ({ x: entry.x, y: entry.y5 })),
                                        yAxisID: 'y',
                                        borderColor: 'rgba(0, 128, 128, 1)', // teal
                                        backgroundColor: 'rgba(0, 128, 128, 1)', // teal
                                        borderWidth: 2,
                                        fill: false,
                                        tension: 1.0,
                                        cubicInterpolationMode: 'default',
                                        pointRadius: 0.2,
                                        showLine: true,
                                        hidden: !dataset1Visible,
                                    };
                                } else {

                                } 


                            // Dataset 6
                                if (value_6.length > 0 && (firstParameterId==firstParameterId_2)) {
                                    console.log("value_6.length = ", value_6.length);
                                    dataset6 = {
                                        label: firstCwmsTsId_6,
                                        data: alignedData.map(entry => ({ x: entry.x, y: entry.y6 })),
                                        yAxisID: 'y', // Assign to the first y-axis
                                        borderColor: 'rgba(255, 255, 0, 1)',
                                        backgroundColor: 'rgba(255, 255, 0, 1)',
                                        borderWidth: 2,
                                        fill: false,
                                        tension: 1.0,
                                        cubicInterpolationMode: 'default',
                                        pointRadius: 0.2,
                                        showLine: true,
                                        hidden: !dataset1Visible,
                                    };
                                } else if (value_6.length > 0) {
                                    console.log("value_6.length = ", value_6.length);
                                    dataset6 = {
                                        label: firstCwmsTsId_6,
                                        data: alignedData.map(entry => ({ x: entry.x, y: entry.y6 })),
                                        yAxisID: 'y2', // Assign to the first y-axis
                                        borderColor: 'rgba(255, 255, 0, 1)', // yellow
                                        backgroundColor: 'rgba(255, 255, 0, 1)', // yellow
                                        borderWidth: 2,
                                        fill: false,
                                        tension: 1.0,
                                        cubicInterpolationMode: 'default',
                                        pointRadius: 0.2,
                                        showLine: true,
                                        hidden: !dataset1Visible,
                                    };
                                } else {

                                }


                            // Dataset 7
                                if (value_7.length > 0) {
                                    console.log("value_7.length = ", value_7.length);
                                    dataset7 = {
                                        label: firstCwmsTsId_7,
                                        data: alignedData.map(entry => ({ x: entry.x, y: entry.y7 })),
                                        yAxisID: 'y', // Assign to the first y-axis
                                        borderColor: 'rgba(255, 165, 0, 1)', // orange
                                        backgroundColor: 'rgba(255, 165, 0, 1)', // orange
                                        borderWidth: 2,
                                        fill: false,
                                        tension: 1.0,
                                        cubicInterpolationMode: 'default',
                                        pointRadius: 0.2,
                                        showLine: true,
                                        hidden: !dataset1Visible,
                                    };
                                } else {

                                }

                            // Dataset 8 FLOOD LEVEL
                                if (value_8.length > 0) {
                                    console.log("value_8.length = ", value_8.length);
                                    dataset8 = {
                                        label: 'Flood',
                                        data: value_8,
                                        yAxisID: 'y', // Assign to the first y-axis
                                        borderColor: 'rgba(0, 0, 255, 1)', // Blue
                                        backgroundColor: 'rgba(0, 0, 255, 1)', // Blue
                                        borderWidth: 4,
                                        fill: false,
                                        tension: 1.0,
                                        cubicInterpolationMode: 'default',
                                        pointRadius: 0.2,
                                        showLine: true,
                                        hidden: !dataset8Visible, // Initially visible or hidden based on flag	
                                    };
                                } else {

                                }

                            // Dataset 9 LWRP
                                if (value_9.length > 0) {
                                    console.log("value_9.length = ", value_9.length);
                                    dataset9 = {
                                        label: 'LWRP',
                                        data: value_9,
                                        yAxisID: 'y', // Assign to the first y-axis
                                        borderColor: 'rgba(139, 69, 19, 1)', // Brown
                                        backgroundColor: 'rgba(139, 69, 19, 1)', // Brown
                                        borderWidth: 4,
                                        fill: false,
                                        tension: 1.0,
                                        cubicInterpolationMode: 'default',
                                        pointRadius: 0.2,
                                        showLine: true,
                                        hidden: !dataset9Visible, // Initially visible or hidden based on flag	
                                    };
                                } else {

                                }

                            // Dataset 10 Hinge Max
                                if (value_10.length > 0) {
                                        console.log("value_10.length = ", value_10.length);
                                        dataset10 = {
                                            label: 'Hinge Max',
                                            data: value_10,
                                            yAxisID: 'y', // Assign to the first y-axis
                                            borderColor: 'rgba(0, 0, 0, 1)', // Black
                                            backgroundColor: 'rgba(0, 0, 0, 1)', // Black
                                            borderWidth: 4,
                                            fill: false,
                                            tension: 1.0,
                                            cubicInterpolationMode: 'default',
                                            pointRadius: 0.2,
                                            showLine: true,
                                            hidden: !dataset10Visible, // Initially visible or hidden based on flag	
                                        };
                                    } else {

                                    }

                            // Dataset 11 Hinge Min
                                if (value_11.length > 0) {
                                        console.log("value_11.length = ", value_11.length);
                                        dataset11 = {
                                            label: 'Hinge Min',
                                            data: value_11,
                                            yAxisID: 'y', // Assign to the first y-axis
                                            borderColor: 'rgba(0, 0, 0, 1)', // Black
                                            backgroundColor: 'rgba(0, 0, 0, 1)', // Black
                                            borderWidth: 4,
                                            fill: false,
                                            tension: 1.0,
                                            cubicInterpolationMode: 'default',
                                            pointRadius: 0.2,
                                            showLine: true,
                                            hidden: !dataset11Visible, // Initially visible or hidden based on flag	
                                        };
                                    } else {

                                    }

                            // lineChart
                                var lineChart = new Chart(ctx, {
                                    type: 'line',
                                    data: {
                                        labels: date_time, // Your X-axis labels
                                        datasets: [
                                            dataset1,
                                            dataset2,
                                            dataset3,
                                            dataset4,
                                            dataset5,
                                            dataset6,
                                            dataset7,
                                            allFloodValuesLessThan900 && firstParameterId === "Stage" && firstCwmsTsId_2 == undefined ? dataset8 : null, 
                                            allLWRPValuesLessThan900 && firstParameterId === "Stage" && firstCwmsTsId_2 == undefined ? dataset9 : null, 
                                            allHingeMaxValuesLessThan900 && firstParameterId === "Stage" && firstCwmsTsId_2 == undefined? dataset10 : null,
                                            allHingeMinValuesLessThan900 && firstParameterId === "Stage" && firstCwmsTsId_2 == undefined? dataset11 : null,
                                        ].filter(Boolean), // Filter out null datasets,
                                    },
                                    options: {
                                        responsive: true,
                                        maintainAspectRatio: true,
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
                                                //min: roundedMinValueAxis_y1,
                                                //max: roundedMaxValueAxis_y1,
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
                                            y2: {
                                                display: false, // Initially hide the right y-axis (y-axis-2)
                                                //min: roundedMinValueAxis_y2,//roundedMinValueAxis_y2,
                                                //max: roundedMaxValueAxis_y2,//roundedMaxValueAxis_y2,
                                                suggestedMin: roundedMinValueAxis_y2,
                                                suggestedMax: roundedMaxValueAxis_y2,
                                                type: 'linear', // Use linear scale for Unit 1
                                                position: 'right', // Position on the left side
                                                title: {
                                                    display: true,
                                                    text: firstParameterId_2 + ' ' + '(' + firstValueUnit_2 + ')', // Add your Y-axis title here
                                                    fontSize: 16, // Adjust the font size if needed
                                                    fontColor: 'black', // Adjust the font color if needed
                                                },
                                                padding: {
                                                    //top: 10,      // Padding at the top of the y-axis
                                                    //bottom: 10,    // Padding at the bottom of the y-axis
                                                },
                                                ticks: {
                                                //stepSize: 10, // Adjust step size to stretch the y-axis
                                                max: 200,     // Adjust max value to stretch the y-axis
                                                }
                                            }
                                        },
                                        plugins: {
                                            tooltip: {
                                                callbacks: {
                                                    label: function(context) {
                                                        var value = context.parsed.y;
                                                        var datasetLabel = context.dataset.label; // Get the label of the dataset
                                                        var isY2Axis = context.dataset.yAxisID === 'y2'; // Check if the data point belongs to Y2-axis
                                                        if (isY2Axis) {
                                                            // Handle Y2-axis specific formatting here if needed
                                                            return value.toFixed(2) + " " + firstValueUnit_2 + " " + datasetLabel + " (Y2-axis)";
                                                        } else {
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


                            // Update or modify your chart data if y2 is used
                                if (firstParameterId==firstParameterId_2) {
                                    // The variable the same
                                    console.log('firstParameterId is equal to firstParameterId_2, therefore dont display y2');
                                } else {
                                    console.log('firstParameterId is not equal to firstParameterId_2, therefore display y2 if there is data');
                                    // The variable not the same
                                    // Place your code here that should run when the variable not the same
                                    // Check datasets on the second y-axis and update y2 display option
                                    var datasetsOnY2Axis = lineChart.data.datasets.filter(function(dataset) {
                                    return dataset.yAxisID === 'y2';
                                    });

                                    if (datasetsOnY2Axis.length > 0) {
                                        lineChart.options.scales.y2.display = true;
                                        // There are datasets on the second y-axis
                                        console.log('Datasets on Y2 axis:', datasetsOnY2Axis);
                                    } else {
                                        lineChart.options.scales.y2.display = false;
                                        // No datasets on the second y-axis
                                        console.log('No datasets on Y2 axis.');
                                    }
                                    lineChart.update();
                                }
                            </script>


                            <script>
                                // PLOT TABLE SCRIPT
                                    console.log("dataToPlot = ", data);

                                    // Function to create the table
                                    function createTable(data, tableId, label) {
                                        // Create a table element
                                        var table = document.createElement("table");
                                        table.id = tableId;
                                        table.style.display = "none";
                                        table.style.width = "100%";
                                        table.style.border = "1px solid #ccc";

                                        // Style for header row
                                        var headerRowStyle = "background-color: #f2f2f2; border: 1px solid #ccc; padding: 8px;";

                                        // Create a header row
                                        var headerRow = table.insertRow(0);
                                        headerRow.style.cssText = headerRowStyle;
                                        var headers = ["Date Time (CST)", "Value", "Unit", "Quality Code"];

                                        // Populate header cells
                                        for (var i = 0; i < headers.length; i++) {
                                            var headerCell = headerRow.insertCell(i);
                                            headerCell.textContent = headers[i];
                                            headerCell.style.padding = "8px";
                                            headerCell.style.border = "1px solid #ccc";
                                        }

                                        // Style for data rows
                                        var dataRowStyle = "background-color: #ffffff; border: 1px solid #ccc;"; // Adding border to each row

                                        // Create rows and populate cells with data
                                        for (var i = 0; i < data.length; i++) {
                                            var row = table.insertRow(i + 1); // Start from index 1 to skip the header row
                                            row.style.cssText = dataRowStyle;

                                            for (var j = 0; j < data[i].length; j++) {
                                                var cell = row.insertCell(j);
                                                // Round Numeric Value 1 to two decimal places

                                                if (j === 1 && data[i][2] === "ft") {
                                                    var numericValue1 = parseFloat(data[i][j]);
                                                    // Use toFixed(2) when the value in the third column (index 2) is "ft"
                                                    cell.textContent = numericValue1.toFixed(2);
                                                } else if (j === 1 && data[i][2] === "cfs") {
                                                    var numericValue1 = parseFloat(data[i][j]);
                                                    // Use toFixed(0) when the value in the third column (index 2) is "cfs"
                                                    cell.textContent = addCommaToCfsValue(numericValue1.toFixed(0));
                                                } else if (j === 1 && data[i][2] === "in") {
                                                    var numericValue1 = parseFloat(data[i][j]);
                                                    // Use toFixed(2) when the value in the third column (index 2) is "in"
                                                    cell.textContent = numericValue1.toFixed(2);
                                                } else {
                                                    cell.textContent = data[i][j];
                                                }
                                                cell.style.border = "1px solid #ccc";
                                                cell.style.textAlign = "left";
                                                cell.style.paddingLeft = "8px";
                                            }
                                        }

                                        // Get the div with id "dataTable"
                                        var dataTableDiv = document.getElementById("dataTable");

                                        // Append the table to the "dataTable" div
                                        dataTableDiv.appendChild(table);

                                        // Create button only if there is data
                                        if (data.length > 0) {
                                            createButton(tableId, label);
                                        }
                                    }

                                    // Function to add comma to "cfs" values
                                    function addCommaToCfsValue(value) {
                                        // Convert value to a number
                                        var numericValue = parseFloat(value);

                                        // Check if the value is greater than 999.99
                                        if (numericValue > 999.99) {
                                            // Add commas to the value
                                            return numericValue.toLocaleString();
                                        } else {
                                            return value;
                                        }
                                    }

                                    // Function to create a button with a label
                                    function createButton(tableId, label) {
                                        var button = document.createElement("button");
                                        button.textContent = label;
                                        button.className = "modern-button-table";
                                        button.style.marginRight = "10px"; // Add left margin
                                        button.style.marginBottom = "10px"; // Add left margin
                                        button.onclick = function() {
                                            toggleTable(tableId);
                                        };

                                        // Append the button to the "buttonContainer" div
                                        document.getElementById("buttonContainer").appendChild(button);
                                    }

                                    // Call the function with your data and labels
                                    createTable(data, 'timeSeriesDataTable', 'Toggle Data 1');
                                    createTable(data_2, 'timeSeriesDataTable_2', 'Toggle Data 2');
                                    createTable(data_3, 'timeSeriesDataTable_3', 'Toggle Data 3');
                                    createTable(data_4, 'timeSeriesDataTable_4', 'Toggle Data 4');
                                    createTable(data_5, 'timeSeriesDataTable_5', 'Toggle Data 5');
                                    createTable(data_6, 'timeSeriesDataTable_6', 'Toggle Data 6');
                                    createTable(data_7, 'timeSeriesDataTable_7', 'Toggle Data 7');

                                    // Function to toggle the visibility of the table
                                    function toggleTable(tableId) {
                                        console.log('Toggling table with ID:', tableId);
                                        var table = document.getElementById(tableId);

                                        if (table) {
                                            table.style.display = table.style.display === "none" ? "table" : "none";
                                            console.log('New display property:', table.style.display);
                                        } else {
                                            console.log('Table with ID ' + tableId + ' not found.');
                                        }
                                    }
                            </script>
                            <!--APP ENDS-->
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
                $('#header').load('../../../templates/INTERNAL.header.php?v=1');
				<?php
					if ($basin=="Mississippi") {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.plotmacros.mississippi.php?v=1');";
					} elseif ($basin=="Illinois") {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.plotmacros.illinois.php?v=1');";
					} elseif ($basin=="Missouri") {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.plotmacros.missouri.php?v=1');";
					} elseif ($basin=="Meramec") {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.plotmacros.meramec.php?v=1');";
					} elseif ($basin=="Tributaries") {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.plotmacros.tributaries.php?v=1');";
					} elseif ($basin=="Mark Twain DO") {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.plotmacros.marktwaindo.php?v=1');";
					} elseif ($basin=="Mark Twain") {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.plotmacros.marktwain.php?v=1');";
					} elseif ($basin=="Wappapello") {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.plotmacros.wappapello.php?v=1');";
					} elseif ($basin=="Shelbyville") {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.plotmacros.shelbyville.php?v=1');";
					} elseif ($basin=="Carlyle") {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.plotmacros.carlyle.php?v=1');";
					} elseif ($basin=="Rend") {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.plotmacros.rend.php?v=1');";
					} elseif ($basin=="Kaskaskia Nav") {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.plotmacros.kaskaskianav.php?v=1');";
					} elseif ($basin=="Water Quality") {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.plotmacros.waterquality.php?v=1');";
					} else {
						echo "$('#sidebar').load('../../../templates/INTERNAL.sidebar.php?v=2');";
					}
				?>
                $('#footer').load('../../../templates/INTERNAL.footer.php?v=1');
            })
        </script>
    </body>
</html>
<?php db_disconnect($db); ?>