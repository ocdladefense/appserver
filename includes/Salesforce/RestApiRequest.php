<?php




namespace Salesforce;


use Http\HttpRequest;
use Http\HttpHeader;
use Http\Http;
use Http\HttpResponse;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Http\BodyPart;


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
        
        // Doesn't work with multipart form data.  Will need to adjust this.
        // if($this->body != null) {
        //     $contentType = new HttpHeader("Content-Type", "application/json");
        //     if($this->getMethod() == "GET")
        //     {
        //     $this->setPost();
        //     }
        //     $this->body = json_encode($this->body);
        //     $this->addHeader($contentType);
        // }

        $config = array(
                "returntransfer" 		=> true,
                "useragent" 				=> "Mozilla/5.0",
                "followlocation" 		=> true,
                "ssl_verifyhost" 		=> false,
                "ssl_verifypeer" 		=> false
        );


        $http = new Http($config);
        
        $resp = $http->send($this);
            
        // $http->printSessionLog();exit;
        // var_dump($resp);
        // exit;
        return $resp;
    }

    public function uploadFile(File $file, $sobjectType = "Attachment"){

        $endpoint = "/services/data/v51.0/sobjects/{$sobjectType}/";

        // $file = File::fromPath($filePath);
    
        $content = file_get_contents($filePath);

        // Get this from the file extension.
        $mimetype = "application/pdf";  
    
    
        $this->setMethod("POST");
        $this->addHeader(new HttpHeader("Content-type", "multipart/form-data; boundary=\"boundary\""));
    

        // Should be json sooner or later
        // replace "folderId" with parent id of object....event remember that?

        // $meatadata will be passed in.
        // $metadata = array(
        //     "Description" => "Marketing brochure for Q1 2011",
        //     "Keywords" => "marketing,sales,update",
        //     "FolderId" => "005D0000001GiU7",
        //     "Name" => "Marketing Brochure Q1",
        //     "Type" => "pdf"
        // );

        $metaContentDisposition = $isDocument ? "form-data; name=\"entity_document\"" : "form-data; name=\"entity_document\"";

        $part1 = new BodyPart();
        $part1->addHeader("Content-Disposition", $contentDisposition);
        $part1->addHeader("Content-Type", "application/json");

        // Make the body part aware of the content type.  If the content type is application/json it should encode it for you
        $part1->setContent($metadata);


        $binaryContentDisposition = $isDocument ? "form-data; name=\"Body\"; filename=\"{$filePath}\"" : "form-data; name=\"Body\"; filename=\"{$filePath}\"";

        $part2 = new BodyPart();
        // File name shoud be the name of the file not the path
        $part2->addHeader("Content-Disposition", $binaryContentDisposition);
        $part2->addHeader("Content-Type", $mimetype);
        $part2->setContent($content);

        $this->addPart($part1);
        $this->addPart($part2);

        return $this->send($endpoint);

    }

    public function uploadDocument($filepath, $description = null){

        $endpoint = "/services/data/v51.0/sobjects/Document/";
    
        // Might need to encode in base64 format
        $content = file_get_contents($filePath);
        $fileType = "application/pdf";  
    
    
        $this->setMethod("POST");
        $this->addHeader(new HttpHeader("Content-type", "multipart/form-data; boundary=\"boundary\""));
    

        // Should be json sooner or later
        // replace "folderId" with parent id of object....event remember that?

        $metadata = array(
            "Description" => "Marketing brochure for Q1 2011",
            "Keywords" => "marketing,sales,update",
            "FolderId" => "005D0000001GiU7",
            "Name" => "Marketing Brochure Q1",
            "Type" => "pdf"
        );


        $part1 = new BodyPart();
        $part1->addHeader("Content-Disposition","form-data; name=\"entity_document");
        $part1->addHeader("Content-Type", "application/json");

        // Make the body part aware of the content type.  If the content type is application/json it should encode it for you
        $part1->setContent($metadata);



        $part2 = new BodyPart();
        // File name shoud be the name of the file not the path
        $part2->addHeader("Content-Disposition","form-data; name=\"Body\"; filename=\"{$filePath}\"");
        $part2->addHeader("Content-Type", $fileType);
        $part2->setContent($content);

        $this->addPart($part1);
        $this->addPart($part2);

        return $this->send($endpoint);
    }

    public function uploadAttachment($filepath, $description = null){

        $endpoint = "/services/data/v51.0/sobjects/Document/";
    
        // Might need to encode in base64 format
        $content = file_get_contents($filepath);
        $fileType = "application/pdf";  
    
    
        $this->setMethod("POST");
        $this->addHeader(new HttpHeader("Content-type", "multipart/form-data; boundary=\"boundary\""));
    

        // Should be json sooner or later
        // replace "folderId" with parent id of object....event remember that?

        $metadata = array(
            "Description" => "Marketing brochure for Q1 2011",
            "Keywords" => "marketing,sales,update",
            "FolderId" => "005D0000001GiU7",
            "Name" => "Marketing Brochure Q1",
            "Type" => "pdf"
        );


        $part1 = new BodyPart();
        $part1->addHeader("Content-Disposition","form-data; name=\"entity_document");
        $part1->addHeader("Content-Type", "application/json");

        // Make the body part aware of the content type.  If the content type is application/json it should encode it for you
        $part1->setContent($metadata);



        $part2 = new BodyPart();
        // File name shoud be the name of the file not the path
        $part2->addHeader("Content-Disposition","form-data; name=\"Body\"; filename=\"{$filepath}\"");
        $part2->addHeader("Content-Type", $fileType);
        $part2->setContent($content);

        $this->addPart($part1);
        $this->addPart($part2);

        return $this->send($endpoint);
    }

    public function uploadFiles(\File\FileList $list, $parentId){

        $endpoint = "/services/data/v51.0/composite/sobjects/";

        $this->setMethod("POST");
        $this->addHeader(new HttpHeader("Content-Type", "multipart/form-data; boundary=\"boundary\""));

        $metadata = $this->buildMetadata($list, $parentId);

        $metaPart = new BodyPart();
        $metaPart->addHeader("Content-Disposition","form-data; name=\"collection");
        $metaPart->addHeader("Content-Type", "application/json");
        $metaPart->setContent($metadata);
        $this->addPart($metaPart);

        $partIndex = 0;
        foreach($list->getFiles() as $file){

            $binaryPart = BodyPart::fromFile($file, $partIndex);
            $this->addPart($binaryPart);
            $partIndex++;
        }

				// var_dump($this);
				
        return $this->send($endpoint);
    }

    public function buildMetadata($fileList, $parentId){

        // Probably want to pass in the type of SObject at some point.

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
		


    public function query($soql) {

        $endpoint = "/services/data/v49.0/query/?q=";
				$endpoint .= urlencode($soql);

        $resp = $this->send($endpoint, "GET");
        //var_dump($resp);
        //exit;
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


    public function insert($sObjectName, $record) {

        $endpoint = "/services/data/v49.0/sobjects/".$sObjectName;

        $contentType = new HttpHeader("Content-Type", "application/json");
        $this->setPost();
        $this->setBody(json_encode($record));
        $this->addHeader($contentType);
    
        
        $resp = $this->send($endpoint);
       
        return $resp->getBody();
    }

    public function update($sObjectName, $record)
    {

        //variables//
        $apiVersion = "v50.0";
        $id = $record->Id;

        //unsets or removes the id from the body//
        unset($record->Id);

        $endpoint = "/services/data/{$apiVersion}/sobjects/{$sObjectName}/{$id}";

        $contentType = new HttpHeader("Content-Type", "application/json");
        $this->setPatch();
        $this->setBody(json_encode($record));
        $this->addHeader($contentType);

        $resp = $this->send($endpoint);
        
        return $resp->getBody();
    }


    public function delete($sObject, $sObjectId)
    {
        $apiVersion = "v50.0";

        $endpoint = "/services/data/{$apiVersion}/sobjects/{$sObject}/{$sObjectId}";

        $this->setDelete();
        $resp = $this->send($endpoint);

        return true;
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