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
        $resp = $this->sendRequest($endpoint,"POST",$records);
        if (strpos($resp->getBody(),"hasErrors:true")){
            throw new Exception($resp->getBody());
        }
        $body = $resp->getBody();


        return $body;
    }
    
    
    
    



    public function getAttachment($id) {
			$endpoint = "/services/data/v49.0/sobjects/Attachment/{$id}/body";
			$resp = $this->sendRequest($endpoint);
			
			return $resp;
    }
    
    




    public function queryIdsFromSession($sObjectName,$ids,$fields){
        $this->authorizeToSalesforce();
        
        
        return $this->queryIds($sObjectName,$ids,$fields,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }



    public function queryIds($sObjectName,$ids,$fields,$instance_url = null,$access_token = null){
        $endpoint = "/services/data/v50.0/composite/sobjects/".$sObjectName."?ids=";
        foreach($ids as $id){
            $endpoint = $endpoint.$id.",";
        }
        $endpoint = rtrim($endpoint, ',');//deleting last comma
        $endpoint = $endpoint."&fields=";
        foreach($fields as $field){

            $endpoint = $endpoint.$field.",";
        }
        $endpoint = rtrim($endpoint, ',');//deleting last comma
        $resp = $this->sendRequest($endpoint);
        

        return $resp->getBody();
    }
    



    public function updateRecordFromSession($sObject = null,$record){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->updateRecord($sObject, $record, $_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }

    public function updateRecord($sObject = null, $record, $instance_url = null, $access_token = null){
        $apiVersion = "v50.0";
        $id = $record->Id;
        unset($record->Id);
        //$endpoint = "/services/sobjects/";
        //services/data/v50.0/sobjects/Account/001D000000INjVe
        $endpoint = "/services/data/{$apiVersion}/sobjects/{$sObject}/{$id}"; 
        //var_dump($record);
        //exit;
        //better way to do the trailing front slash
        $resp = $this->sendRequest($endpoint."/","PATCH",$record);
        
        
        return $resp->getBody();
    }




    public function updateRecordsFromSession($records,$sObject = null){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->updateRecords($records,$sObject,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }

    public function updateRecords($records, $sObject = null, $instance_url = null, $access_token = null){
        
        $singularEndpoint = "/services/sobjects/";
        $pluralEndpoint = "/services/data/v49.0/composite/sobjects/";
        $plural = is_array($records) && isset($records[0]);
        
        if($plural){
            foreach($records as $record){
                if(!isset($record["attributes"])){
                    throw new Exception ("Attribute field not set");
                }
            }
        }
        $endpoint = $plural ? $pluralEndpoint : $singularEndpoint.$sObject."/".$records[0]["Id"];

        $fn = function ($record,$index) use($sObject){
            $record["attributes"] = array("type"=>$sObject);
            return $record;
        };
        $records = $plural && $sObject!= null ? array_map($fn,$records,array_keys($records)):$records;
        $records = $plural ? array("records" => $records ) : $records;
        if($plural){
            $records["allOrNone"] = false;
        }

        //better way to do the trailing front slash
        $resp = $this->sendRequest($endpoint."/","PATCH",$records);
        
        
        return $resp->getBody();
    }



    public function deleteRecordFromSession($sObject,$sObjectIds){
        return $this->deleteRecordsFromSession($sObject,$sObjectIds);
    }



    public function deleteRecordsFromSession($sObject,$sObjectIds){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->deleteRecords($sObject,$sObjectIds,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }
    
    
    
    public function deleteRecords($sObject, $sObjectIds, $instance_url = null, $access_token = null) {
        $pluralEndpoint = function () use($sObjectIds){
            $endpoint = "/services/data/v49.0/composite/sobjects?ids=";
            foreach ($sObjectIds as $value)
                $endpoint = $endpoint.$value.",";
            return $endpoint."&allOrNone=false";
        };
        //$singularEndpoint = "/services/data/v50.0/sobjects/".$sObject."/".$sObjectIds;
        $endpoint = is_array($sObjectIds)? $pluralEndpoint."/" : "/services/data/v49.0/sobjects/".$sObject."/".$sObjectIds."/";
        $resp = $this->sendRequest($endpoint,"DELETE");

        $body = $resp->getBody();
        //var_dump($resp);
        if(is_array($sObjectIds) && $resp->getStatusCode() != 200){
            throw new Exception("Status Code: ".$resp->getStatusCode()." Error deleating the record(s): ".$resp->getBody());
            
        }
        else if(!is_array($sObjectIds) && $resp->getStatusCode() != 204){
            throw new Exception("Status Code: ".$resp->getStatusCode()." Error deleating the record(s): ".$resp->getBody());
            
        }
        
        
        return true;
    }



    public function addToBatch($fields, $metod = null){
        $req = array();//final request to add to batch

        if(empty($fields) && (!is_array($fields) || !is_string($fields))){
            throw new Exception("Invalid request");
        }
        if(!is_array($fields) && strpos($fields,"SELECT")){
            $req["url"] = "v50.0/query/?q=".urlencode($fields);
            $req["method"] = "GET";
            array_push($req);
            return $this->reqBody;
        }



        // if($req["method"] == "PATCH" &&  strpos($req["url"],"v50.0/sobjects/")){//if its calling the update
        //     if(empty($req["richInput"]) || !is_array($req["richInput"])){
        //         throw new Exception("Invalid richInput of an UPDATE/PATCH");
        //     }
        //     if (array_keys($req["richInput"]) !== range(0, count($req["richInput"]) - 1)){
        //         throw new Exception("Invalid richInput BODY of an UPDATE/PATCH");
        //     }
        //     if(count($req) != 3){
        //         throw new Exception("Invalid UPDATE/PATCH, malformed elements");
        //     }

        //     if($this->reqBody == null){
        //         $this->reqBody = array(
        //             "batchRequests" => array($req)
        //         );
        //     }
        //     array_push($this->reqBody["batchRequests"],$req);
        // }
        // if($req["method"] == "GET" &&  strpos($req["url"],"v50.0/sobjects/")){//if its calling the update
        //     if(strpos($req["url"],"?fields=")){
        //         throw new Exception("Invalid url of QUERY/GET or not encoded ");
        //     }
        //     if(count($req) != 2){
        //         throw new Exception("Invalid QUERY/GET, malformed elements");
        //     }
        //     if($this->reqBody == null){
        //         $this->reqBody = array(
        //             "batchRequests" => array($req)
        //         );
        //     }
        //     array_push($this->reqBody["batchRequests"],$req);
        // }
        // if($req["method"] == "POST" &&  strpos($req["url"],"v50.0/sobjects/")){//if its calling the update
        //     if(empty($req["richInput"]) || !is_array($req["richInput"])){
        //         throw new Exception("Invalid richInput of an CREATE/POST");
        //     }
        //     if (array_keys($req["richInput"]) !== range(0, count($req["richInput"]) - 1)){
        //         throw new Exception("Invalid richInput BODY of an CREATE/POST");
        //     }
        //     if(count($req) != 3){
        //         throw new Exception("Invalid CREATE/POST, malformed elements");
        //     }

        //     if($this->reqBody == null){
        //         $this->reqBody = array(
        //             "batchRequests" => array($req)
        //         );
        //     }
        //     array_push($this->reqBody["batchRequests"],$req);
        // }

        // if($req["method"] == "DELETE" &&  strpos($req["url"],"v50.0/sobjects/")){//if its calling the update
        //     if(empty($req["richInput"]) || !is_array($req["richInput"])){
        //         throw new Exception("Invalid richInput of an CREATE/POST");
        //     }
        //     if (array_keys($req["richInput"]) !== range(0, count($req["richInput"]) - 1)){
        //         throw new Exception("Invalid richInput BODY of an CREATE/POST");
        //     }
        //     if(count($req) != 2){
        //         throw new Exception("Invalid CREATE/POST, malformed elements");
        //     }

        //     if($this->reqBody == null){
        //         $this->reqBody = array(
        //             "batchRequests" => array($req)
        //         );
        //     }
        //     array_push($this->reqBody["batchRequests"],$req);
        // }


    }



    public function sendBatchFromSession($reqBody = null){
        $authResult = $this->authorizeToSalesforce();
        if (!$authResult->isSuccess()) {
            throw new SalesforceAuthException("Not Authorized");
        }
        return $this->deleteRecords($reqBody,$_SESSION["salesforce_instance_url"],$_SESSION["salesforce_access_token"]);
    }



    public function sendBatch($reqBody = null, $instance_url = null, $access_token = null) {
        if(empty($reqBody)){
            $reqBody = $this->reqBody;
        }
        if (!is_array($reqBody)){
            throw new Exception("request body is not an array");
        }
        

        $endpoint = "/services/data/v50.0/composite/batch/";
        $resp = $this->sendRequest($endpoint,"POST",array("batchRequests" => $reqBody));
        if(strpos($resp->getBody(),"\"hasErrors\" : true")){
            throw new Exception($resp->getBody());
        }
        
        
        return $resp->getBody();
    }



}