<?php

namespace Http;


//remove virtural keyword in class definition?
class HttpMessage {




	/**
	 * An array of Http headers to be 
	 *  sent with thhe message body.
	 */
	protected $headers = array();
	
	/**
	 * Indicates whether this message has been
	 *  securely signed.
	 */
	protected $isSigned = false;
	


	
	protected $params = array();


	public function __construct(){
	
	}

	public function setHeaders(array $headers) {
		$this->headers = $headers;
	}
	
	public function addHeaders(array $headers) {
		$this->headers = array_merge($this->headers,$headers);
	}


	/**
	 * Return the header with the specified name.
	 *  If more than one header with this name
	 *  exists, then return the last one.
	 *
	 *  http spec supports multiple headers with the same name,
	 *  however, multiple pseudo-headers of the same name are prohibited.
	 */
	public function getHeader( $name ) {

		if(strpos($name,":") === 0 || $name == "(request-target)") {
		
			
			return $this->getPseudoHeader($name);
		}
		
		
		$filter = function($header) use ($name) {
			return $name == $header->getName();			
		};
		
		$tmp = array_filter($this->headers, $filter);

		if(null == $tmp || count($tmp) < 1) return null;
		
		$arrange = array_values($tmp);
		

		return $arrange[count($arrange)-1];
	}



	private function getPseudoHeader($name) {
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

	
	
	public function getHeaders(){
		return $this->headers;
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


	public function sign(SigningRequest $sr, SigningKey $key) {

		$headerString = $sr->signHeaders($this);
		
		$keyId = new SignatureParameter("keyid", $key->getKeyId());
		$algo = new SignatureParameter("algorithm", $sr->getAlgorithm());
		$signedHeaders = new SignatureParameter("headers", $sr->getSignedHeaders());
		$signature = new SignatureParameter("signature", 
			SigningRequest::generateSignature($headerString,$key));
		
		$bag = new SignatureParameterBag(
			$keyId,
			$algo,
			$signedHeaders,
			$signature
		);
		
		$this->headers []= new HttpHeader("Signature",$bag->__toString());
		
		$this->isSigned = true;
	}

	// https://tools.ietf.org/id/draft-cavage-http-signatures-07.html#rfc.section.1.1
	public function getRequestTarget($method, $path, $resourceId = null) {
		if(null != $resourceId) {
			$resourcePath = $method . " " . $path .$resourceId;
		} else {
			$resourcePath = $method . " " . $path;
		}
		
		$this->headers["(request-target)"] = utf8_encode($resourcePath);
	}

}