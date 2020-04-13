<?php
// https://developer.cybersource.com/api/developer-guides/dita-gettingstarted/authentication/GenerateHeader/httpSignatureAuthentication.html#id193AL0O0BY4_id199ILK00CHS

// https://developer.cybersource.com/api-reference-assets/index.html#transaction-details_transactiondetails_retrieve-a-transaction

// https://github.com/CyberSource/cybersource-rest-samples-php/blob/master/Samples/Authentication/StandAloneHttpSignature.php
/**
 * https://tools.ietf.org/id/draft-cavage-http-signatures-07.html#rfc.section.1.1
 * 
 * https://tools.ietf.org/id/draft-cavage-http-signatures-07.html#RFC4648
 */
class SigningRequest {

		// Key that will be used to sign this message.
		private $signingKey = null;
		
		
		// Header names, ordered, that will be used to generate
		//  the signature.
		private $orderedNames;



    public function __construct() {}
    

		// ‘map’ above is the HashMap of all the five headers discussed below.

		/*
		* these specific "set.." methods should be removed very soon.
		*/
		public function signHeaders($orderedNames) {
			$this->orderedNames = $orderedNames;
		}
		
		
		public function setHost($host) {
			$this->headers["host"] = $host;
		}
		
		public function setDate($date) {
			$this->headers["date"] = $date;
		}
		
		// https://tools.ietf.org/id/draft-cavage-http-signatures-07.html#rfc.section.1.1
		public function setRequestTarget($method, $path, $resourceId = null) {
			if(null != $resourceId) {
				$resourcePath = $method . " " . $path .$resourceId;
			} else {
				$resourcePath = $method . " " . $path;
			}
			
			$this->headers["(request-target)"] = utf8_encode($resourcePath);
		}
		
		public function setMerchantId($merchantId) {
			$this->headers["v-c-merchant-id"] = $merchantId;
		}
		/*
		* these specific "set.." methods should be removed very soon.
		*/
		
		
		
		
		
		
    // The Shared Secret Key should be Base64-encoded and used to sign the signature parameter.
    /** 
     *SecretKeySpec secretKey = 
     *	new SecretKeySpec(Base64.getDecoder().decode(“YOUR SHARED SECRET KEY”), "HmacSHA256");
		 * 
		 *  HttpSignatureHeader httpSignatureHeader =
			*  HttpSignature.createHttpSignatureHeaders(messageBody, map, secretKey);
			*
			* See also:
			* https://developer.cybersource.com/api/
			*  developer-guides/dita-gettingstarted/authentication/GenerateHeader/
			*			httpSignatureAuthentication.html#id193AL0O0BY4_id199ILK00CHS
			*
			*  See also:
			*  https://developer.cybersource.com/library/documentation/
			*			dev_guides/REST_API/Getting_Started/Getting_Started_REST_API.pdf
			*
			*  See also:
			*  https://github.com/CyberSource/
			*		cybersource-rest-samples-php/blob/
			*			master/Samples/Authentication/StandAloneHttpSignature.php
			*/
    public function signWith(SigningKey $key) {
    
    	$this->signingKey = $key;
    	
    }
    
    
    /**
     * Needs to be refactored
     *  so that we are not hard-conding
     *  references to the header names:
     *
     * the names are already stored in $this->orderedNames
     */
    public function signHeaders($headers) {
    	
    		// foreach($this->orderedNames blah, blah, blah)
    
				$host = $headers["host"];
				$date = $headers["date"];
				$requestTarget = $headers["(request-target)"];
				$merchantId = $headers["v-c-merchant-id"];


				$signatureString = "host: " . $host . "\ndate: ". $date . "\n(request-target): ".$requestTarget."\nv-c-merchant-id: ".$merchantId;

        
        return utf8_encode($signatureString);
    }
    
    
    
    public function generateSignature($headerString) {
    
    		if(null == $this->signingKey) {
    			throw new \Exception("MISSING_KEY_ERROR: Cannot generate signature without a key.");
    		}
    
				$headers = array();
				$asBinary = true;



        // $this->signedValue = hash_hmac("sha256", $headers, $key->getBase64(), true));
				return base64_encode(hash_hmac("sha256", $byteString, $this->signingKey->decode(), $asBinary));
    }

    
}