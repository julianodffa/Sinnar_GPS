<?php


$data ="$Longitude $Latitude $Speed ".date("F-j-Y-g:i:s-a") . PHP_EOL;
// echo $data;

$myfile = fopen("gps.log", "a") or die("Unable to open file!");
fwrite($myfile, $data);
fclose($myfile);
?>