<?php

/** 
 *SecretKeySpec secretKey = 
 *	new SecretKeySpec(Base64.getDecoder().decode(“YOUR SHARED SECRET KEY”), "HmacSHA256");
 * 
 *  HttpSignatureHeader httpSignatureHeader =
	*  HttpSignature.createHttpSignatureHeaders(messageBody, map, secretKey);
	Search Results
		Featured snippet from the web
		PEM is a X. 509 certificate (whose structure is defined using ASN. 1), encoded using the ASN. 1 DER (distinguished encoding rules), then run through Base64 encoding and stuck between plain-text anchor lines (BEGIN CERTIFICATE and END CERTIFICATE)
	*/
	
namespace Http;
	
class SigningKey {


	// new SecretKeySpec(Base64.getDecoder().decode(“YOUR SHARED SECRET KEY”), "HmacSHA256");		
	private $base64key = null;
	
	private $keyId;
		


	public function __construct($keyId){
		$this->keyId = $keyId;
		$this->base64key = $this->loadKey($keyId);
	}
	
	public function loadKey($keyId) {
		global $keyStore;

		if(!isset($keyStore) || !isset($keyStore[$keyId])) {
			throw new \Exception("{$keyId} does not point to a valid key.");
		}

		return $keyStore[$keyId];
	}
	

	
	public function test($secretToCompare) {
		if(null == $secretToCompare) return false;
		
		return $secretToCompare === $this->base64key;
	}

		
	/**
	 * Base64 Key
	 */
	public function decode() {

		if(null == $this->base64key) {
			throw new \Exception("Key is null.");
		}
		
		return base64_decode($this->base64key);
	}
	
	
	public function getKeyId() {
		return $this->keyId;
	}
	/**
	 * Load the .PEM key from a file
	 */
	public static function fromFile($filePath) {
	
	}
}