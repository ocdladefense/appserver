<?php

namespace Http;


//remove virtural keyword in class definition?
class HttpMessage {



	protected $body;


	protected $acceptResponse = "Http\HttpResponse";


	protected $curlInfo;


	protected $headers;
	
	/**
	 * Indicates whether this message has been
	 *  securely signed.
	 */
	protected $isSigned = false;
	
	protected $params;
	


	public function __construct(){
		$this->headers = new HttpHeaderCollection();
	}


	//body
	public function setBody($body){
		$this->body = $body;
	}

	public function getBody(){
		return $this->body;
	}
	public function getContentType(){
		return $this->getHeader('Content-Type')->getValue();
	}


	//headers
	public function getHeaderCollection(){
		return $this->headers;
	}


	public function setHeaders(array $headers) {
		if($this->isSigned) throw new \Exception("INVALID HEADER OPERATION");
		//$this->headers->reset();
		$this->headers->addHeaders($headers);
	}
	
	public function addHeaders(array $headers) {
		if($this->isSigned) throw new \Exception("INVALID HEADER OPERATION");
		$this->headers->addHeaders($headers);
	}


	public function addHeader(HttpHeader $header) {
		if($this->isSigned) throw new \Exception("INVALID HEADER OPERATION");
		$this->headers->addHeader($header);
	}

	public function getHeaders(){
		return $this->headers;
	}

	
	public function setStripOwsFromHeaders($names = array()) {
		$this->headers->setStripOwsFromHeaders($names);
	}


	public function getHeader($name) {
		if(strpos($name,":") === 0 || $name == "(request-target)") {
			return $this->getPseudoHeader($name);
		}
		
		return $this->headers->getHeader($name);
	}

	protected function getPseudoHeader($name) {
		if($name == ":method") {
			
			return new HttpHeader($name,strtolower($this->method));
		} else if($name == ":scheme") {
		
		} else if($name == ":authority") {
		
		} else if($name == ":path") {
		
			return new HttpHeader($name,strtolower($this->path));
		} else if($name == "(request-target)") {
			
			$value = strtolower($this->method) . " " .strtolower($this->path);
			return new HttpHeader($name,$value);
		}
	}

	//curlInfo
	public function setCurlInfo($info){
		$this->curlInfo = $info;
	}

	public function getCurlInfo(){
		return $this->curlInfo;
	}

	public function getDate() {
		$header = $this->getHeader('Date');
		return $header->getValue();
	}
	

	/**
	 * Convert an array of HttpHeader objects
	 *  to a PHP keyed array.
	 */
	public static function toArray(array $headers) {
		return array_map(function($header) use($stripOwsFromHeaders){
			return $header->getName() . ": ".$header->getValue();
		},$headers);
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

	

	public function getSignature(){
		$sig = $this->getHeader("Signature");
		
		if(null == $sig) {
			throw new \Exception("Signature not defined.");
		}
		
		return $sig->getValue();
	}
	
	public function sign(SigningRequest $sr, SigningKey $key) {

		$signatureParameter = $sr->signMessage($this,$key);



		
		$headerInventory = implode(" ", $sr->getHeaderInventory());
		$keyId = new SignatureParameter("keyid", $key->getKeyId());
		$algo = new SignatureParameter("algorithm", $sr->getAlgorithm());
		$signedHeaders = new SignatureParameter("headers", $headerInventory);
		$signature = new SignatureParameter("signature", $signatureParameter);
		
		$bag = new SignatureParameterBag(
			$keyId,
			$algo,
			$signedHeaders,
			$signature
		);



		$this->addHeader(new HttpHeader("Signature",$bag->__toString()));
		
		$this->isSigned = true;
	}

	// https://tools.ietf.org/id/draft-cavage-http-signatures-07.html#rfc.section.1.1
	public function getRequestTarget($method, $path, $resourceId = null) {
		if(null != $resourceId) {
			$resourcePath = $method . " " . $path .$resourceId;
		} else {
			$resourcePath = $method . " " . $path;
		}
		
		return $this->headers["(request-target)"] = utf8_encode($resourcePath);
	}


	public function setAccept($className){
		$this->acceptResponse = $className;
	}

	public function getAccept(){
		return $this->acceptResponse;
	}
}