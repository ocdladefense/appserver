<?php
// this class is responsible for storing any given Probe into a new JSON file titled with the name of the url and the
// date the probe was performed
class ProbeResult {

    $domain;
    $url;
    $expectedResponse;
    $actualResponse;
    $contentType;
    $responseTime;
    $requestMethod;
    $startDate;
    $endDate;
    $requestedBy;

    constructor($response)

    public save() {

    }
}