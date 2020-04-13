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
		


    public function __construct($sharedSecret){
    	$this->base64key = $sharedSecret;
    }
    
		public function setSharedSecret($sharedSecret) {
			$this->base64key = $sharedSecret;
		}

		
    /**
     * Base64 Key
     */
		public function decode() {
        return base64_decode($this->sharedSecret);
		}
		
		
		
		/**
		 * Load the .PEM key from a file
		 */
		public static function fromFile($filePath) {
		
		}
}