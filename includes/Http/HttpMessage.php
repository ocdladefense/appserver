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




	public function setHeaders(array $headers) {
		$this->headers = $headers;
	}


	/**
	 * Return the header with the specified name.
	 *  If more than one header with this name
	 *  exists, then return the last one.
	 */
	public function getHeader( $name ) {
		$tmp = array_filter($this->headers,function($header) {
			return $name == $header->getName();
		});
		
		return $tmp[count($tmp)-1];
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


	public function sign(SigningRequest $sr) {

		$headerString = $sr->signHeaders($this);
		
		$keyId = new SignatureParameter("keyid", $sr->getKeyId());
		$algo = new SignatureParameter("algorithm", $sr->getAlgorithm());
		$signedHeaders = new SignatureParameter("headers", $sr->getSignedHeaders());
		$signature = new SignatureParameter("signature", $sr->generateSignature($headerString));
		
		$bag = new SignatureParameterBag(
			$keyId,
			$algo,
			$signedHeaders,
			$signature
		);
		
		$header = new HttpHeader("Signature",$bag->__toString());
		
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