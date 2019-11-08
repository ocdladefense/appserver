<?php
// this class is used to load, validate, and probe domain records

class DomainManager {
    // used to select all .json files
    private static $DOMAIN_RECORD_VALID = 0;
    private static $DOMAIN_RECORD_INVALID = 1;

    private $domainRecords;
    private $probes;

    private $listOfDomains;
    private $overallSiteResponseTime;
    private $startTime;
    private $endTime;

    function __construct() {
        define("SITE_HEALTHY", 1);
        define("SITE_UNHEALTHY", 2);
    }

    function getTime() {
        return microtime(true);
    }

    // newFromFileSystem takes a folder path and creates an array of specific file paths as strings
    // then it loads each file path into an object and adds it to the $this->domainRecords array
    // and returns a DomainManager object 
    static function newFromFileSystem($folderPath) {
        $manager = new DomainManager();

        $filePaths = FileSystem::getFileList($folderPath, $fileExtension = ".json");

        foreach($filePaths as $filePath) {
            $manager->makeDomainRecord($filePath);
        }

        return $manager;
    }

    // create a domain record object and add it to the $this->domainRecords array
    function makeDomainRecord($filePath) {
        $domainRecord = new DomainRecord();

        $domainRecord = FileSystem::convertJsonFileToObject($filePath);

        $this->domainRecords[$filePath] = $domainRecord;

        // for unit testing
        return $domainRecord;
    }

    public static function validate($domainRecord) {
        if(self::getState($domainRecord) == self::$DOMAIN_RECORD_INVALID) {
            throw new DomainManagerException($domainRecord, self::$DOMAIN_RECORD_INVALID);
        }
    }

    function doValidation() {

        foreach($this->domainRecords as $domainRecord) {

            DomainManager::validate($domainRecord);

            // // ----- FOR TESTING ONLY -----
            // echo "The .json file for ".$file->domain." passed validiation. \n";
        }
    }

    public static function getState($domainRecord) {

        // check to see that a domain key exists and that the value starts with "http". If either is false, validation should fail
        if(!isset($domainRecord->domain)) {
            return self::$DOMAIN_RECORD_INVALID;
        } else if (!preg_match("/^http/", $domainRecord->domain)) {
            return self::$DOMAIN_RECORD_INVALID;            
        }

        // check that there is a "probes" key. If not, validation should fail and return immediately
        if(!isset($domainRecord->probes)) {
            return self::$DOMAIN_RECORD_INVALID;
        }

        // check that there is a "path" key in every probe. If not, validation should fail
        foreach($domainRecord->probes as $probe) {
            if(!isset($probe->path))
                return self::$DOMAIN_RECORD_INVALID;
        }

        return self::$DOMAIN_RECORD_VALID;
    }

    function doProbes() {
        $this->startTime = $this->getTime();
        
        foreach($this->domainRecords as $domainRecord) {
            $this->probes[] = DomainManager::probe($domainRecord);
        }

        $this->endTime = $this->getTime();
    }

    function getProbes() {
        return $this->probes;
    }

    static function probe($domainRecord) {
        $probe = new Probe($domainRecord);
        return $probe;
    }

    function renderOutput() {
        print_r("All probes completed successfully in ".($this->endTime - $this->startTime)." seconds! Check '/results' for .JSON file results.");
    }
}

// FOR COMMAND LINE TESTING
// $probeManager = new ProbeManager('C:\\wamp64\\www\\trust\\appserver\\site-json\\');
?>
