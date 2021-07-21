<?php

namespace Salesforce;

use Http\HttpRequest;
use Http\HttpHeader;
use Http\Http;
use Http\HttpResponse;
use Http\HttpException;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Http\BodyPart;
use File\File;


class RestApiRequest extends HttpRequest {

    public const SALESFORCE_EXPIRED_ACCESS_TOKEN_ERROR = "INVALID_SESSION_ID";

    public $resourcePrefix = "/services/data";

	private $instanceUrl;
	
	private $accessToken;

    private $addXHttpClientHeader = true;



	public const ENDPOINTS = array(
        "sObject Basic Information" => array(
            "endpoint" => "/%apiVersion/sobjects/%sObject/",
            "parameters" => array(
                "version" => 0,
                "sObjectName" => 2
            )
        ),
        "Query" => array(
            "endpoint" => "/%apiVersion/query/?q=",
            "parameters" => array(
                "version" => 0,
                "soql" => "q"
            )
        )
    );


    public function __construct($instanceUrl, $accessToken) {
    
    	parent::__construct();

    	$this->instanceUrl = $instanceUrl;
    	$this->accessToken = $accessToken;
    }


    public function send($endpoint) {

        if(empty($this->instanceUrl)) throw new HttpException("REST_API_ERROR:  The instance url cannot be null.");
        if(empty($this->accessToken)) throw new RestApiException("REST_API_ERROR:  The access token cannot be null.");
    
        $this->setUrl($this->instanceUrl . $endpoint);
        
        if($this->addXHttpClientHeader){

            $this->addHeader(new HttpHeader("X-HttpClient-ResponseClass","\Salesforce\RestApiResponse")); // Use a custom HttpResponse class to represent the HttpResponse.
        }

        $token = new HttpHeader("Authorization", "Bearer " . $this->accessToken);
        $this->addHeader($token);
        
        $config = array(
                "returntransfer" 		=> true,
                "useragent" 			=> "Mozilla/5.0",
                "followlocation" 		=> true,
                "ssl_verifyhost" 		=> false,
                "ssl_verifypeer" 		=> false
        );

        $http = new Http($config);
        
        $resp = $http->send($this, true);

        // Is it safe to assume that if a RestApiRequest fails due to an expired access token, that we are using the usernamepassword flow?
        if($resp->getErrorCode() == self::SALESFORCE_EXPIRED_ACCESS_TOKEN_ERROR){

            $updatedRequest = refresh_user_pass_access_token($this);

            return $http->send($updatedRequest, true);
        }

        return $resp;
    }

    public function removeXHttpClientHeader(){

        $this->addXHttpClientHeader = false;
    }

    public function setAccessToken($token){

        $this->accessToken = $token;
    }

    public function getAccessToken(){

        return $this->accessToken;
    }

    public function getInstanceUrl(){

        return $this->instanceUrl;
    }

    public function getEndpoint($target, $version = "v51.0" , $getIndex = false){

        return ENDPOINT[$target][$version];
    }

    public function uploadFile(SalesforceFile $file){

        $sObjectName = $file->getSObjectName();

        $isAttachment = $sObjectName == "Attachment";

        $endpoint = "/services/data/v51.0/sobjects/{$file->getSObjectName()}/";
    
        $method = "POST"; // By default we will insert new records.

        if($isAttachment && $file->getId() != null){

            $method = "PATCH";

        } else if(!$isAttachment && $file->getContentDocumentId() != null){ //You cant do patch request for content versions.

            $method = "POST";
        }
        
        $this->setMethod($method);
        $this->setContentType("multipart/form-data; boundary=\"boundary\"");
    

        $metaContentDisposition = $isAttachment ? "form-data; name=\"entity_document\"" : "form-data; name=\"entity_content\"";

        $metaPart = new BodyPart();
        $metaPart->addHeader("Content-Disposition", $metaContentDisposition);
        $metaPart->addHeader("Content-Type", "application/json");
        $metaPart->setContent($file->getSObject());

        $binaryContentDisposition = $isAttachment ? "form-data; name=\"Body\"; filename=\"{$file->getName()}\"" : "form-data; name=\"VersionData\"; filename=\"{$file->getName()}\"";

        $binaryPart = new BodyPart();
        $binaryPart->addHeader("Content-Disposition", $binaryContentDisposition);
        $binaryPart->addHeader("Content-Type", $file->getType()); 
        $binaryPart->setContent($file->getContent());

        $this->addPart($metaPart);
        $this->addPart($binaryPart);

        $resp = $this->send($endpoint);

        if(!$resp->isSuccess()){

			$message = $resp->getErrorMessage();
			throw new \Exception($message);
		}

        return $resp;
    }


    public function uploadFiles(\File\FileList $list, $parentId){

        $endpoint = "/services/data/v51.0/composite/sobjects/";

        $this->setMethod("POST");
        $this->addHeader(new HttpHeader("Content-Type", "multipart/form-data; boundary=\"boundary\""));

        $metadata = $this->buildMetadata($list, $parentId);

        $metaPart = new BodyPart();
        $metaPart->addHeader("Content-Disposition","form-data; name=\"collection\"");
        $metaPart->addHeader("Content-Type", "application/json");
        $metaPart->setContent($metadata);
        $this->addPart($metaPart);

        $partIndex = 0;
        foreach($list->getFiles() as $file){

            $binaryPart = BodyPart::fromFile($file, $partIndex);
            $this->addPart($binaryPart);
            $partIndex++;
        }
				
        return $this->send($endpoint);
    }

    public function buildMetadata($fileList, $parentId){

        $metadata = array(
            "allOrNone" => false,
            "records"   => array()
        );

        for($i = 0; $i < $fileList->size(); $i++){

            $file = $fileList->getFileAtIndex($i);

            $metadata["records"][] = array(

                "attributes" => array(
                    "type"   => "Attachment",
                    "binaryPartName" => "binaryPart{$i}",
                    "binaryPartNameAlias" => "Body"
                ),
                "Description" => $file->getName(),
                "ParentId"    => $parentId,
                "Name"        => $file->getName()
            );

        }

        return $metadata;
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

        $resp = $this->send($endpoint);
                
        return $resp->getBody();
    }

    public function query($soql) {

        $endpoint = "/services/data/v49.0/query/?q=";
        $endpoint .= urlencode($soql);

        $this->setMethod("GET");

        $resp = $this->send($endpoint);

        return $resp;
    }

    public function upsert($sobjectName, $record){

        // Set up the endpoint.
        $baseUrl = "/services/data/v49.0/sobjects/" . $sobjectName;
        $endpoint = $record->Id == null || $record->Id == "" ? $baseUrl : $baseUrl . "/" . $record->Id;

        $record = self::formatJson($record);

        // Set up the request.
        $record->Id == null || $record->Id == "" ? $this->setPost() : $this->setPatch();
        $this->setContentType("application/json");
        unset($record->Id);
        $this->setBody(json_encode($record));

        $resp = $this->send($endpoint);

        return $resp;
    }


    public function delete($sObject, $sObjectId){
        $apiVersion = "v50.0";

        $endpoint = "/services/data/{$apiVersion}/sobjects/{$sObject}/{$sObjectId}";

        $this->setDelete();
        $resp = $this->send($endpoint);

        return $resp;
    }

    public static function formatJson($record){

        foreach($record as $key => $value){

            if(is_string($value) && trim($value) == ""){

                $record->$key = null;
            }
            if(trim($value) == "on"){

                $record->$key = True;
            }
        }

        return $record;
    }

    public function getAttachment($id) {
        
        $endpoint = "/services/data/v49.0/sobjects/Attachment/{$id}/body";
        $resp = $this->send($endpoint);

        return $resp;
    }

    public function getAttachments($parentId) {
        $endpoint = "/services/data/v49.0/sobjects/Attachment/{$parentId}/body";
        $resp = $this->send($endpoint);

        return $resp;
    }

    public function getDocument($id) {
        $endpoint = "/services/data/v49.0/sobjects/Document/{$id}/body";
        $resp = $this->send($endpoint);

        return $resp;
    }
    
    public function getDocuments($parentId) {
        $endpoint = "/services/data/v49.0/sobjects/Attachment/{$parentId}/body";
        $resp = $this->send($endpoint);

        return $resp;
    }

    public function getContentDocument($id) {
           
        $endpoint = "/services/data/v51.0/sobjects/ContentVersion/{$ContentVersionId}/VersionData";
        $resp = $this->send($endpoint);

        return $resp;
    }

    public function getContentDocuments($parentId) {

           
        $endpoint = "/services/data/v51.0/sobjects/ContentDocumentLink/{$ContentDocumentID}";
        $resp = $this->send($endpoint);

        return $resp;
    }
}