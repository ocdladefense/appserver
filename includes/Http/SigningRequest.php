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


	const STRIP_OWS = 0x001;
	
	const PRESERVE_OWS = 0x002;
	
	private $ows;
	
	private $algorithm;



  public function __construct($opts = array()) {
  	$this->ows = isset($opts["ows"]) ? $opts["ows"] : self::PRESERVE_OWS;
  }
    

	// ‘map’ above is the HashMap of all the five headers discussed below.

	/*
	* these specific "set.." methods should be removed very soon.
	*/
	public function headersToSign($orderedNames) {
		$this->orderedNames = $orderedNames;
	}
	
	public function getHeaderInventory() {
		return explode(" ",$this->orderedNames);
	}
	
	public function setAlgorithm($algo) {
		$this->algorithm = $algo;
	}
	
	public function getAlgorithm(){
		return $this->algorithm;
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

    
    private static $HTTP_STANDARD_HEADERS = array(
    	"date","host","content-type","accept","digest"
    );
    

    public function getHeaders(HttpMessage $msg) {
    
    	$ordered = $this->getHeaderInventory();

    	
			foreach($ordered as $name) {
		
				$actual = in_array(strtolower($name),self::$HTTP_STANDARD_HEADERS) ? ucfirst($name) : $name;
			
				$header = $msg->getHeader($actual);
			
				if( null == $header ) {
					throw new \Exception("MESSAGE_SIGNING_ERROR: missing header at {$actual}.  This signing request requires that this header be present in the header inventory.");
				}
			
				$separator = ": ";//$this->ows == self::PRESERVE_OWS ? ": " : ":";
				
				$temp[] = strtolower($header->getName()).$separator. $header->getValue();
			}
			
			return $temp;
    }
    
    
    
    /**
     * Needs to be refactored
     *  so that we are not hard-conding
     *  references to the header names:
     *
     * the names are already stored in $this->orderedNames
     */
    public static function getEncodedHeaders(array $headers) {

			return utf8_encode(implode("\n", $headers));
    }
    
    public static function hash($valueToHash, SigningKey $signingKey) {
		
    
			if(null == $signingKey) {
				throw new \Exception("MISSING_KEY_ERROR: Cannot generate signature without a key.");
			}

			$asBinary = true;

			// Check for empty/null keys and throw an Exception.
			$decoded = $signingKey->decode();
			if(null == $decoded) {
				throw new \Exception("Decode key is malformed.");
			}
			
			
			return base64_encode(hash_hmac("sha256", $valueToHash, $decoded, $asBinary));
	}

	public function signMessage(HttpMessage $msg, SigningKey $key){

		if($msg->getMethod() == "POST"){
			$prefix = "SHA-256=";
			$digest = $prefix . SigningRequest::hash($msg->getBody(),$key);
			$msg->addHeader(new HttpHeader("Digest",$digest));
		}
		$headers = $this->getHeaders($msg);
		
		$headerKeyValues = SigningRequest::getEncodedHeaders($headers);

		return SigningRequest::hash($headerKeyValues,$key);
	}

	// function generateDigest($requestBody,$requiresKey = false){

	// 	if($requiresKey){
	// 		$hash = base64_encode(hash_hmac("sha256", $requestBody, $decoded, $asBinary));
	// 	} else {
	// 		$hash = base64_encode(hash_hmac("sha256", $requestBody));
	// 	}
		
	// 	$prefix = "SHA-256=";
		
	// 	return $prefix . $hash;
	// }
}



