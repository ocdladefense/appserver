<?php

namespace Http;

	
class Http {



	private static $recordSentHeaders = false;


	private static $headersSent = null;	


	private $overrideHeaders;
	
	
	private $config;


	private $httpSessionLog;



	public function setOverrideHeaders($headers = array()) {
		$this->overrideHeaders = $headers;
	}
	
	
	
	
	
	// Get the cURL configuration object.
	//  It has convenience methods to change the curl configuration.
	public function __construct($config = null) {

		$this->config = new CurlConfiguration($config);
	}


	// Send the specified HttpMessage, optionally
	//   enable logging.
	public function send(HttpMessage $msg, $log = false) {

		
		// Static context so need to reset headers before further processing.
		self::$headersSent = null;
		
		// Convert from array of HttpHeaders to a string array
		// conforming to cURL spec.

		$headers = !isset($this->overrideHeaders) ? $msg->getHeaders()->getHeadersAsArray() : $this->overrideHeaders;
		$this->config->setHeaders($headers);
		
		
		// For POST requests we need to change the request
		// type from GET to POST *and we need
		// to set the CURLOPT_POSTFIELDS options to the body
		// of our request (JSON, etc.)
		if($msg->isPost()) {
			if(gettype($msg->getBody()) != "string"){
				throw new \Exception("INVALID_TYPE_ERROR: MESSAGE BODY MUST BE A STRING");
			}

			$this->config->setPost();
			$this->config->setBody($msg->getBody());
			
		} elseif(!$msg->isGet()) {
		
			$this->config->setMethod($msg->getMethod());
		}
		
		
		
		
		// print_r(HttpHeader::toArray($msg->getHeaders()));
		// Send using cURL with the 
		$resp = Curl::send($msg->getUrl(), $this->config->getAsCurl());

		//var_dump($resp);exit;
		

		$logArray = explode(" * ",$resp["log"]);
		
		
		
		// $logMessage = implode("<br />",$logArray);
		$this->httpSessionLog = $logArray;

		$accept = $msg->getAccept() == null ? "Http\HttpResponse" : $msg->getAccept();
		
		
		// Return a new instance of HttpResponse(); 
		return self::newHttpResponse(
			$accept,
			$resp["headers"],
			$resp["body"],
			$resp["info"]
		);
	}

	public function getSessionLog(){
		return $this->httpSessionLog;
	}
	

	private static function newHttpResponse($accept,$headers,$body,$info,$log = null){
		$resp = new $accept($body);
		$resp->setHeaders(HttpHeader::fromArray($headers));
		$resp->setCurlInfo($info);
		$resp->setStatusCode($info["http_code"]);
		return $resp;
	}



	public static function getSentHeaders() {
		return self::$headersSent;
	}


	public static function recordSentHeaders($boolean = true) {
		self::$recordSentHeaders = $boolean;
	}



	/**
	 * Nothing to do here. Leave for now -José
	 */
	public static function fromCurl($header,$body,$info) {

		// Handle the response
		if ($response_info['http_code'] === 0) {
				$curl_error_message = curl_error($curl);

				// curl_exec can sometimes fail but still return a blank message from curl_error().
				if (!empty($curl_error_message)) {
						$error_message = "API call to $url failed: $curl_error_message";
				} else {
						$error_message = "API call to $url failed, but for an unknown reason. " .
								"This could happen if you are disconnected from the network.";
				}

				$exception = new \Exception($error_message, 0, null, null);
				// $exception->setResponseObject($response_info);
				throw $exception;
		} elseif ($response_info['http_code'] >= 200 && $response_info['http_code'] <= 299) {
				$stream_headers['http_code'] = $response_info['http_code'];
		
				return [$http_body, $stream_headers['http_code'], $stream_headers];
		} else {

				/*
				throw new \Exception(
						"[".$response_info['http_code']."] Error connecting to the API ($url)",
						$response_info['http_code'],
						$stream_headers,
						null
				);
				*/
		}

	}
	
}