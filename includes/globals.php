<?php
function getPathToModules(){
    return  __DIR__ ."/../modules";
}
function getPathToContent(){
    return __DIR__."/../content";
}
//returns the path to directory at the root level
function getPath($dir){
    $path = __DIR__."/../".$dir;
    return $path;
}
function filterScanResults($results){
    $unfilteredResults = $results;
    $filteredResults = array();

    foreach($unfilteredResults as $unfiltered){
        if($unfiltered != "." && $unfiltered != ".."){
            $filteredResults[] = $unfiltered;
        }
    }
    return $filteredResults;
}