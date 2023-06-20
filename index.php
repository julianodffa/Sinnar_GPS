<?php
$filename = "gps.json";

if (isset($_GET['Longitude'])) {
    $Longitude = $_GET['Longitude'];
    $Latitude = $_GET['Latitude'];
    $Speed = $_GET['Speed'];
    $data = [$Longitude, $Latitude, $Speed];
    $json = json_encode($data);
    file_put_contents("gps.json", $json);
    $data ="$Longitude $Latitude $Speed ".date("F-j-Y-g:i:s-a") . PHP_EOL;
    // echo $data;

    $myfile = fopen("gps.log", "a") or die("Unable to open file!");
    fwrite($myfile, $data);
    fclose($myfile);
}

if (file_exists($filename)) {
    $data = file_get_contents("gps.json");
    $json = json_decode($data, true);
    $Longitude = $json[0];
    $Latitude = $json[1];
    $Speed = $json[2];
} else {
    $Longitude = 107.808556;
    $Latitude = -6.263601;
    $Speed = 0;
}



function baca_log_gps($path_file)
{
    $file = fopen($path_file, "r");
    $array = [];
    // var_dump(fread($file,filesize("gps.log")));
    while (($line = fgets($file)) !== false) {
        // echo $line;
        $res = explode(" ", $line);
        array_push($array, $res);
        // print_r($res);
    }
    // print_r(array_reverse($array));
    return array_reverse($array);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPS Test</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <!-- Make sure you put this AFTER Leaflet's CSS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        #map {
            height: 180px;
        }
    </style>
</head>

<body>
    <h1>Show Longitude, Latitude, and Speed(KM/h)</h1>
    <hr>
    <span>Longitude: <?= $Longitude ?></span><br>
    <span>Latitude: <?= $Latitude ?></span><br>
    <span>Speed(KM/h): <?= $Speed ?></span><br>

    <div id="map"></div>

    <?php 
    $result = baca_log_gps("gps.log");
    echo "<pre>".print_r($result,2)."</pre>";
    ?>

    <script>
        var map = L.map('map').setView([<?= $Latitude ?>, <?= $Longitude ?>], 10);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        var marker = L.marker([<?= $Latitude ?>, <?= $Longitude ?>]).addTo(map);
    </script>
</body>

</html>