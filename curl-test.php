<?php

run();

function run(){
    
    $page = "https://eugeneweekly.com/";

    //header("Location: $page");

    $output = fopen("php://output", "w");
    $curl = curl_init();







    $response = curl_exec($curl);

    var_dump($response);


}