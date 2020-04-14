<?php
// https://developer.cybersource.com/api/developer-guides/dita-gettingstarted/authentication/GenerateHeader/httpSignatureAuthentication.html#id193AL0O0BY4_id199ILK00CHS

// https://developer.cybersource.com/api-reference-assets/index.html#transaction-details_transactiondetails_retrieve-a-transaction

// https://github.com/CyberSource/cybersource-rest-samples-php/blob/master/Samples/Authentication/StandAloneHttpSignature.php
/**
 * https://tools.ietf.org/id/draft-cavage-http-signatures-07.html#rfc.section.1.1
 * 
 * https://tools.ietf.org/id/draft-cavage-http-signatures-07.html#RFC4648
 */
namespace Http;
 
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
		public function headersToSign($orderedNames) {
			$this->orderedNames = $orderedNames;
		}
		
		
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
    public function signHeaders(HttpMessage $message){
			print "<pre>" .print_r($message->getHeaders,true)."</pre>";
			exit;
			$temp = array();
			foreach(explode(" ",$this->orderedNames) as $name){
				$header = $message->getHeader($name);
				if( null == $header ) {
					throw new \Exception("MESSAGE_SIGNING_ERROR: missing header at {$name}.");
				}
				
				$temp[] = $header->getName().": " . $header->getValue();
			}

				
			return utf8_encode(implode("\n", $temp));
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