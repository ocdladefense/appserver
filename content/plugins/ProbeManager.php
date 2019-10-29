<?php
// this class is used to Probe every single JSON file in the site-JSON folder

define("BASE_PATH",__DIR__);

include BASE_PATH."/ProbeManagerException.php";
include BASE_PATH."/Probe.php";

class ProbeManager {
    private $GLOB_FILE_EXTENSION = "*.json";

    private $startDate;
    private $endDate;
    private $listOfDomains;

    function __construct($folderPath) {

        // get the start date of the probe and format it
        $this->startDate = getDate();
        $this->startDate = date("Y-m-d-H-i-s");

        // append the json file extension to the folder path
        $folderPath .= $this->GLOB_FILE_EXTENSION;

        $files = $this->readFileDirectory($folderPath);

        $this->loopThroughFiles($files);

    }

    function readFileDirectory($folderPath) {

        // get every json file in the folder path and put it into $files
        $files = glob($folderPath);
        return $files;
    }

    function loopThroughFiles($files) {

        foreach($files as $file) {

            // parse the file to json
            $jsonFile = $this->parseJson($file);

            // validate the json file
            if(!$this->validate($jsonFile)) {
                throw new ProbeManagerException($file);
            }
            echo $file." passed validiation. \n";
        }
    }

    function parseJson($file) {
        $handle = fopen ($file, "r");
        $json = fread($handle,filesize($file));
        $jsonFile = json_decode($json);

        return $jsonFile;
    }

    function validate($file) {
        $fileValid = true;
        // echo "Name: ".$file->name." \n";
        // echo "Domain: ".$file->domain." \n";

        // check to see that domain exists and that it starts with "http". If either is false, validation should fail
        if(isset($file->domain)) {
            if(!preg_match("/^http/", $file->domain))
                $fileValid = false;
        } else {
            $fileValid = false;
        }

        // check that there is at least one probe path value. If not, validation should fail
        if(!isset($file->probes[0]->path))
            $fileValid = false;

        return $fileValid;
    }

    function probe($file) {

    }

}

// for testing in the console
$test = new ProbeManager('C:\\wamp64\\www\\trust\\appserver\\site-json\\');
?>
