<?php

namespace Http;


abstract virtual class HttpMessage {

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
	
	

	public function sign(SigningRequest $sr) {
		$names = $sr->getHeaders();
		
		$vals = $this->getHeaders($names);
		
		$headerString = $sr->signHeaders($vals);
		
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

}