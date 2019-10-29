<?php
// this class is used to Probe a single site and make a new ProbeResult
class Probe {

$url
$expectedStatusCode

constructor(url, expectedStatusCode)

run() {
    // make a new request object for the site
    $request = new HTTPRequest($site->domain);
    $response = $request->makeHTTPRequest();
    ProbeResult $result = new ProbeResult($response);
    return $result;
}

}
