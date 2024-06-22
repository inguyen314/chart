<?php
$basin = $_POST['basin'];

$basinTimeSeriesMapping = [
    "Mississippi" => "St Louis-Mississippi.Stage.Inst.30Minutes.0.lrgsShef-rev",
    "Illinois" => "Meredosia-Illinois.Stage.Inst.30Minutes.0.lrgsShef-rev",
    // Add other basins and their corresponding time series IDs here
];

$tsId = $basinTimeSeriesMapping[$basin] ?? '';
echo json_encode(['tsId' => $tsId]);
?>
