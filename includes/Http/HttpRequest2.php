<?php


namespace Http;


class HttpRequest2 {


	// Where should this HttpMessage be sent?
	private $url = null;
	
	private $params = array();

	private $headers = array();
	
	private $headersSent = array();

	private $requestType = "GET";
	
	// Default to empty body for GET request.
	private $body = "";
	
	private $port;





	public function __construct($url){
		$this->url = $url;
	}

	public function getUrl() {
		return $this->url;
	}





	public function setHeaders(array $headers) {
		$this->headers = $headers;
	}



	/*
	* Purpose : This function calling the Authentication and making an Auth Header
	*
	*/
	public function callAuthenticationHeader($method, $postData, $resourcePath) {
			$merchantConfig = $this->merchantConfig;
			
			$authenticationType = GlobalParameter::HTTP_SIGNATURE; //@jbernal
			
			$authentication = new Authentication();
			
			$getToken = $authentication->generateToken($resourcePath, $postData, $method, $merchantConfig); 
			
			
			//if($authenticationType == GlobalParameter::HTTP_SIGNATURE) {
			$host = "Host:".$merchantConfig->getHost();
			$vcMerchant = "v-c-merchant-id:".$merchantConfig->getMerchantID();
			$date = date("D, d M Y G:i:s ").GlobalParameter::GMT;
			
			$headers = array(
					$vcMerchant,
					$getToken,
					$host,
					'Date:'.$date
			); 

			array_push($headers, "v-c-client-id:" . $this->clientId);

			// if ($merchantConfig->getSolutionId() != null && trim($merchantConfig->getSolutionId()) != '')
			// {
					// array_push($headers, "v-c-solution-id:" . $merchantConfig->getSolutionId());
			// }
			

			
			
			return $headers;
	}


    /**
     * Get Client ID 
     * 
     * @return String
     */
    public function getClientId()
    {
        $versionInfo = "";
        $packages = json_decode(file_get_contents(__DIR__ . "/../../../../vendor/composer/installed.json"), true);

        foreach ($packages as $package) {
            if (strcmp($package['name'], "cybersource/rest-client-php") == 0)
            {
                $versionInfo = "cybs-rest-sdk-php-" . $package['version'];
            }
        }

        return $versionInfo;
    }


	public function getHeader($headerName){
		//throw an exception
		return $this->headers[$headerName];
	}
	
	
	public function getHeaders(){
		return $this->headers;
	}
	

	





	
	public function setPost(){
		$this->requestType = "POST";
	}
	
	
	public function setPatch(){
		$this->requestType = "PATCH";
	}
	
	
	public function setDelete(){
		$this->requestType = "DELETE";
	}
	
	
	public function getRequestType(){
		return $this->requestType;
	}
	
	
	public function setPort($port) {
		$this->port = $port;
	}
	




	
	public function getBody() {
		return $this->body;
	}



	

	
	public function isSupportedContentType($contentType){
		if($this->getHeader("Accept") == $contentType || stringContains($this->headers["Accept"], "*/*")){
			return true;
		}
		return false;
	}
	
	


	






	public function setParams($p){
	  if(is_array($p)) {
			$_params = array();
			foreach($p as $key=>$value){
				$_params[] = $key ."=".$value;
			}		
			$this->params = implode('&',$_params);
	  }
	  else {
		  $this->params = $p;
	  }

	}
	
	
}
