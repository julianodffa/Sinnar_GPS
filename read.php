<?php

$result = baca_log_gps("gps.log");
print_r($result);

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
