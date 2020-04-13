<?php

namespace Http;


//remove virtural keyword in class definition?
class HttpMessage {

	/**
	 * An array of Http headers to be 
	 *  sent with thhe message body.
	 */
	private $headers = array();
	
	/**
	 * Indicates whether this message has been
	 *  securely signed.
	 */
	private $isSigned = false;
	
	public function getHeader($name){
		return $this->headers[$name];
	}

	public function getMethod(){

	}
	
	public function getPath(){

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