<?php




namespace Salesforce;



use Http\HttpRequest;
use Http\HttpHeader;
use Http\Http;
use Http\HttpResponse;
// use phpDocumentor\Reflection\DocBlock\Tags\Throws;







class OAuthRequest extends HttpRequest {


    private $oauth_config = array();
    
    // Per Gino, but by extending HttpRequest
    // we get $this->body for free.
    // private $reqBody = array();


    private const MAX_LOGIN_ATTEMPTS = 3; 



    public function __construct($url) {
        parent::__construct($url);
				// $loginAttempts = !isset($_SESSION["login_attempts"]) ? 0 : $_SESSION["login_attempts"] + 1;
				
				// $_SESSION["login_attempts"] = $loginAttempts;
    }



		/**
		 * Prepare authentication parameters for the Salesforce REST API.
		 *  Keep track of the number of login attempts.
		 */
		public static function fromConfig($config) {
		

				
				$url = $config["oauth_url"];
				
				$req = new OAuthRequest($url);

        $body = array(
            "grant_type" 			=> "password",
            "client_id" 			=> $config["client_id"],
            "client_secret"		=> $config["client_secret"],
            "username"				=> $config["username"],
            "password"				=> $config["password"] . $config["security_token"]
        );
        
				$body = http_build_query($body);
				$contentType = new HttpHeader("Content-Type", "application/x-www-form-urlencoded");
				$req->addHeader($contentType);
				
				$req->setBody($body);
				$req->setMethod("POST");
				// $req->addHeaders($headers);
				$req->setAccept("\Salesforce\OAuthResponse");
				
				// return a redirect under which circumstances?
				// $config["callback_url"]
				return $req;
		}





		
		public function authorize() {
			
			// Use a custom HttpResponse class to represent the HttpResponse.

			$config = array(
					"returntransfer" => true,
					"useragent" => "Mozilla/5.0",
					"followlocation" => true,
					"ssl_verifyhost" => false,
					"ssl_verifypeer" => false
			);


			$http = new Http($config);
			$resp = $http->send($this);
			
			// $http->printSessionLog();
			// var_dump($resp);
			// exit;
			
			return $resp;
		}
		
    


    
    /**
    	* Use an OAuth 2.0 username/password flow 
    	*  for authorizing to Salesforce.
    	*  After authorizing we can use the REST API.
    	*  
    	*  If the session already has an access_token saved,
    	*  then attempt to use it before re-authenticating.
    	*
    	* @return RespApiResult
    	*/
    public static function init() {
    
    		$resp = new HttpResponse();
    		
    		$body = new stdClass();
    		// $body->
    
    		// No need to re-authenticate.
				if(!empty($_SESSION["salesforce_access_token"])) {
				
				}
        $_SESSION["login_attempts"]++;
    
				// We're authenticating, so reset any previous variables.
        unset($_SESSION["salesforce_access_token"]);
        unset($_SESSION["salesforce_instance_url"]);


        //commented out to get around too many login errors~not sure why I am getting that message//
        if($_SESSION["login_attempts"] > self::MAX_LOGIN_ATTEMPTS) {
            throw new Exception ("OAUTH_AUTHENTICATION_ERROR: Too many login attempts.");
        }





        if($resp->isSuccess()) {
            $_SESSION["login_attempts"] = 0;
            $_SESSION["salesforce_instance_url"] = $resp->getInstanceUrl();
            $_SESSION["salesforce_access_token"] = $resp->getAccessToken();
        } else {
            throw new SalesforceAuthException("Not Authorized");
        }
        
        
    }
    
    


		private static function isValidSalesforceUsername($username) {
					//checking username for @ and .
					return strpos($username,"@") !== false && strpos($username,".") !== false;
		}



		private static function isValidOAuthTokenUrl($url) {

			//checking oauth url for .salesforce.com/services/oauth2/token
			return strpos(strtolower($url),".salesforce.com/services/oauth2/token") !== false;
		}

}