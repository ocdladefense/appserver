<?php


// Where should this function go?  globals.php?
function calculateFileSize($bytes) {

    //$bytes = 2000000;
    $kilobytes = $bytes/1024;

    if($kilobytes > 1000) {	

        return round($kilobytes/1000, 1) . " MB";

    } else if($kilobytes > 1){

        return round($kilobytes) . " KB";

    } else {
        return $bytes . " Bytes";
    }
}


