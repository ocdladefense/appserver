<?php
// this class is responsible for storing the results of any given Probe into a new JSON file titled with the name of the domain
// and the date the probe was performed
class ProbeResult {

    private $resultsPath = BASE_PATH."\\results\\";
    private $comments = "1 = good to go, 2 = trouble";

    private $domain;
    private $contentType;
    private $totalResponseTime;
    private $overallStatus;
    private $requestMethod;
    private $startDate;
    private $endDate;
    private $requestedBy;

    private $fileName;
    private $probeResult = array();

    function __construct($file, $startDate, $endDate, $statusCodeResults, $responseTimeResults) {

        $this->domain = $file->domain;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        // // ----- FOR TESTING ONLY -----
        // print_r("Domain Name: ".$this->domain." ");
        // print_r("Start Date: ".$startDate." ");
        // print_r("End Date: ".$endDate." ");

        $this->calculateTotalResponseTime($responseTimeResults);
        $this->calculateOverallStatus($file, $statusCodeResults);

        // package the path results into an array
        $probePathResult = $this->packageProbePathResult($file, $statusCodeResults, $responseTimeResults);

        // print_r($probePathResult);

        // package the full probe results into an array
        $probeResult = $this->packageFullProbeResult($file, $probePathResult);

        // save the full probe result array as a .json file
        $this->saveFullProbeResult($probeResult);
    }

    function getTotalResponseTime() { return $this->totalResponseTime; }

    function calculateTotalResponseTime($responseTimeResults) {

        // add all the individual path response times together
        foreach($responseTimeResults as $responseTime) {
            $this->totalResponseTime += (float) $responseTime;
        }

        // // ----- FOR TESTING ONLY -----
        // print_r("Total Response Time: ".$this->totalResponseTime." ");
    }

    function calculateOverallStatus($file, $statusCodeResults) {

        $this->overallSiteStatus = SITE_HEALTHY;
        
        // compare all path expected status codes to the actual status codes
        foreach($file->probes as $probe) {
            $expectedStatusCode = $probe->expectedStatusCode;
            $actualStatusCode = $statusCodeResults[$probe->path];

            // // ----- FOR TESTING ONLY -----
            // print_r("Expected Code: ".$expectedStatusCode." Actual Code: ".$actualStatusCode." ");

            if($expectedStatusCode != $actualStatusCode)
                $this->overallSiteStatus = SITE_UNHEALTHY;
        }

        // // ----- FOR TESTING ONLY -----
        // print_r("Overall Site Status: ".$this->overallSiteStatus." ");
    }

    function packageProbePathResult($file, $statusCodeResults, $responseTimeResults) {
        $probePathResult = array();
        $i = 0;

        // create an object for each path and store it in the $probePathResult array
        foreach($file->probes as $probe) {
            $object = (object) [
                'path' => $probe->path,
                'expectedStatusCode' => $probe->expectedStatusCode,
                'actualStatusCode' => $statusCodeResults[$probe->path],
                'responseTime' => $responseTimeResults[$probe->path]
            ];

            $probePathResult[$i] = $object;
            $i++;
        }

        return $probePathResult;
    }

    function packageFullProbeResult($file, $probePathResult) {
        
        // create a file name for the result using the name and the start date of the probe
        $this->fileName = $file->name."-".$this->startDate;

        // create an object for the result
        $probeResult = (object) [
            'name' => $file->name,
            'domain' => $this->domain,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalResponseTime' => $this->totalResponseTime,
            'overallSiteStatus' => $this->overallSiteStatus,
            'comments' => $this->comments,
            'probeResults' => $probePathResult
        ];

        return $probeResult;
    }

    function saveFullProbeResult($probeResult) {
        
        // create a new .json file for the result in the results folder
        $handle = fopen($this->resultsPath.$this->fileName.".json", 'w');

        // save the result object to the file
        fwrite($handle, json_encode($probeResult,JSON_UNESCAPED_SLASHES));
        fclose($handle);
    }
}