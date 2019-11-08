<?php
// this class is used to Probe a single site and make a new ProbeResult
class Probe {

    private $domain;
    private $statusCodeResults;
    private $responseTimeResults;
    private $startDate;
    private $endDate;

    public $result;

    function __construct($file) {
        $this->probeDomain($file);
        return $this->result;
    }

    function getDate() {
        $date = getDate();
        $date = date("Y-m-d-H-i-s");
        return $date;
    }

    function probeDomain($file) {
        // get the start date of the probe
        $this->startDate = $this->getDate();

        // store the current domain in a class level variable
        $this->domain = $file->domain;

        // probe all the paths for the current domain
        $this->probePaths($file);

        // get the end date of the probe
        $this->endDate = $this->getDate();

        // after all the paths have been probed, pass the orginal file and the results to a new ProbeResult
        // to store the results in a new .JSON file
        $this->result = new ProbeResult($file, $this->startDate, $this->endDate, $this->statusCodeResults, $this->responseTimeResults);
    }

    function probePaths($file) {
        // create arrays to hold the results of each path probe
        $this->statusCodeResults = array();
        $this->responseTimeResults = array();

        foreach($file->probes as $probe) {
            // get the current path to probe
            $path = $probe->path;

            // make a new request object for the path
            $request = new HTTPRequest($this->domain.$path);

            // make the HTTPRequest (it seems like $response is not needed for anything)
            $response = $request->makeHTTPRequest();

            // put the result of the path probe into respective arrays using $path as the key
            $this->statusCodeResults[$path] = $request->getStatus();
            $this->responseTimeResults[$path] = $request->getInfo()['total_time'];

            // // ----- FOR TESTING ONLY -----
            // print_r("Path: ".$this->domain.$path." ");
            // print_r("Expected Status Code: ".$probe->expectedStatusCode." ");
            // print_r("Actual Status Code: ".$this->statusCodeResults[$path]." ");
            // print_r("Response Time: ".$this->responseTimeResults[$path]." ");
        }
    }
}
