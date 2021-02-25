<?php




namespace Salesforce;


use Http\HttpRequest;
use Http\HttpHeader;
use Http\Http;
use Http\HttpResponse;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;




class RestApiRequest extends HttpRequest {


	private $instanceUrl;
	
	
	private $accessToken;
	
	
	private $contentType;
	
	
	

		/**
		 * Prepare authentication parameters for the Salesforce REST API.
		 *  Keep track of the number of login attempts.
		 */
    public function __construct($instanceUrl, $accessToken) {
    
    	parent::__construct();
    	
    	$this->instanceUrl = $instanceUrl;
    	$this->accessToken = $accessToken;
    }



		
	

		public function send($endpoint) {
		
				$this->setUrl($this->instanceUrl . $endpoint);
				$this->setAccept("\Salesforce\RestApiResponse"); // Use a custom HttpResponse class to represent the HttpResponse.
				$token = new HttpHeader("Authorization", "Bearer " . $this->accessToken);
				$this->addHeader($token);
				
				
				if($this->body != null) {
                    $contentType = new HttpHeader("Content-Type", "application/json");
                    if($this->getMethod() == "GET")
                   {
                    $this->setPost();
                   }
                    $this->body = json_encode($this->body);
					$this->addHeader($contentType);
				}

				$config = array(
						"returntransfer" 		=> true,
						"useragent" 				=> "Mozilla/5.0",
						"followlocation" 		=> true,
						"ssl_verifyhost" 		=> false,
						"ssl_verifypeer" 		=> false
				);


                $http = new Http($config);
				
				$resp = $http->send($this);
				
			// $http->printSessionLog();
			// var_dump($resp);
			// exit;
			return $resp;
		}
		


    public function query($soql) {

        $endpoint = "/services/data/v49.0/query/?q=";
				$endpoint .= urlencode($soql);

        $resp = $this->send($endpoint, "GET");
        
        return json_decode($resp->getBody(), true);
    }



    public function addToBatch($sObjectName, $record, $method = null){
        $req = array();//final request to add to batch

        if($method == "POST"){

            $req["method"] = $method;
            $req["url"] = "v49.0/sobjects/".$sObjectName;
            $req["richInput"] = $record;
        }
        
        return $req;
    }
    


    public function sendBatch($records, $sObjectName) {

        $batches = array();
        foreach($records as $record){
            
            $batches[] = $this->addToBatch($sObjectName, $record, "POST");
        }

        $endpoint = "/services/data/v50.0/composite/batch";

        $foobar = array("batchRequests" => $batches);
        $this->body = $foobar;

        //var_dump($this->body); exit;
        $resp = $this->send($endpoint);
                
    
        //var_dump($resp);
        return $resp->getBody();
    }
//added this function from previousrestapi file//
    public function createRecords($sObjectName, $records) {
        $pluralEndpoint = "/services/data/v49.0/composite/tree/".$sObjectName;
        $singularEndpoint = "/services/data/v49.0/sobjects/".$sObjectName;
        $plural = is_array($records) && isset($records[0]);
        $endpoint = $plural ? $pluralEndpoint : $singularEndpoint;
        $fn = function ($record,$index) use($sObjectName){
            $record["attributes"] = array("type"=>$sObjectName,"referenceId"=>"ref".++$index);
            return $record;
        };
        $records = $plural ? array_map($fn,$records,array_keys($records)):$records;
        $records = $plural ? array("records" => $records ) : $records;
        $resp = $this->send($endpoint);
        if (strpos($resp->getBody(),"hasErrors:true")){
            throw new Exception($resp->getBody());
        }
        $body = $resp->getBody();


        return $body;
    }

    public function insert($sObjectName, $record) {
    
        $endpoint = "/services/data/v49.0/sobjects/".$sObjectName;
        $this->setBody($record);
        $resp = $this->send($endpoint);

        return $resp->getBody();
    }

    public function getAttachment($ContentVersionId) {
           
        $endpoint = "/services/data/v51.0/sobjects/ContentVersion/{$ContentVersionId}/VersionData";
        $resp = $this->send($endpoint);

        return $resp;
    }
}