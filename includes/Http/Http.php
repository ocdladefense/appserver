<?php

namespace Http;

	const HTTP_METHOD_GET = "GET";
	
	const HTTP_METHOD_POST = "POST";
	
	const HTTP_METHOD_PUT = "PUT";
	
	const HTTP_METHOD_DELETE = "DELETE";
	
	const MIME_TEXT_HTML = "text/html; charset=utf-8";
	
	const MIME_APPLICATION_JSON = "application/json; charset=utf-8";
	
	const MIME_TEXT_JAVASCRIPT = "text/javascript";
	

	
	
class Http {



	private static $recordSentHeaders = false;



	private static $headersSent = null;	



	public static function headersToArray(array $headers) {
		return array_map(function($header) {
			return $header->getName() . ": ".$header->getValue();
		},$headers);
	}

	public static function send(HttpMessage $msg) {

		$url = $msg->getUrl();
		//curl_setopt($this->handle, CURLOPT_ENCODING, '');
		ob_start();
		$out = fopen('php://output', 'w');
		//$f = fopen($logFile, 'a');
		if(!$out) throw new Exception("Could not open PHP output stream.");

		$curl = curl_init();
		
		$headers = self::headersToArray($msg->getHeaders());
		
		curl_setopt($curl, CURLOPT_CAINFO,"/var/www/trust/vendor/cybersource/rest-client-php/lib/ssl/cacert.pem");
		
		curl_setopt($curl, CURLOPT_URL, $msg->getUrl());

		curl_setopt($curl, CURLOPT_VERBOSE,true);
		
		//curl_setopt($curl, CURLOPT_STDERR ,$out);	

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		
		curl_setopt($curl, CURLOPT_USERAGENT, "Swagger-Codegen/1.0.0/php");
		
		curl_setopt($curl, CURLOPT_HEADER, 1);

		if(self::$recordSentHeaders) {
			curl_setopt($curl, CURLINFO_HEADER_OUT, true);
		}
		
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);



		$response = curl_exec($curl);
		
		if(self::$recordSentHeaders) {
			self::$headersSent = curl_getinfo($curl, CURLINFO_HEADER_OUT);
		}



		//@jbernal from apiclient line 313		
		//else {
		$http_header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$http_header = self::httpParseHeaders(substr($response, 0, $http_header_size));
		$http_body = substr($response, $http_header_size);
		$response_info = curl_getinfo($curl);
		
		
		curl_close($curl);

		fclose($out);  
		$debug = ob_get_clean();



		print "<h2>DEBUG IS:</h2>";
		print str_replace("\n","<br />",$debug);
		
		
		// Return a nnew instance of Response();     
		return self::newHttpResponse($http_body,$http_header,$response_info);
		
	
	}

	private static function newHttpResponse($body,$header,$response_info){
		$res = new Response();
		$res->setBody($body);
		$res->setHeaders($header);
		$res->setCurlInfo($response_info);
		var_dump($res);exit;

	}


		/**
		 * Should return a new instance of Response.
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
	
	
	

	public static function getSentHeaders() {
		return self::$headersSent;
	}


	public static function recordSentHeaders($boolean = true) {
		self::$recordSentHeaders = $boolean;
	}



   /**
    * Return an array of HTTP response headers
    *
    * @param string $raw_headers A string of raw HTTP response headers
    *
    * @return string[] Array of HTTP response heaers
    */
    private static function httpParseHeaders($raw_headers)
    {
        // ref/credit: http://php.net/manual/en/function.http-parse-headers.php#112986
        $headers = [];
        $key = '';

        foreach (explode("\n", $raw_headers) as $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], [trim($h[1])]);
                } else {
                    $headers[$h[0]] = array_merge([$headers[$h[0]]], [trim($h[1])]);
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) === "\t") {
                    $headers[$key] .= "\r\n\t".trim($h[0]);
                } elseif (!$key) {
                    $headers[0] = trim($h[0]);
                }
                trim($h[0]);
            }
        }

        return $headers;
    }






	public function setHeaders(array $headers){

		foreach( $headers as $header ) {
		
			if($header instanceOf HttpHeader) {
				$this->headers[$header->getName()] = $header->getValue();
			} else {
				$this->headers[] = $header;
			}
		}
	}





	/**
	 * $f = fopen('request.txt', 'w');
			curl_setopt($ch,CURLOPT_VERBOSE,true);
			curl_setopt($ch,CURLOPT_STDERR ,$f);	
	*/
	private function sendHeaders() {
	
		$headers = array();
		
		foreach($this->headers as $name => $value) {
			if(strpos($value,":") !== false) {
				$header = $value;
			} else {
				$header = ($name .": " .$value);
			}
			
			$headers[]= $header;
		}		
			
		curl_setopt($this->handle, CURLOPT_HTTPHEADER, $headers);			
	}




	
	public function getRequestUri(){
		return $this->headers["Request-URI"];
	}




	

	public function userAgent($ua) {
		$this->ua = $ua;
	}


	public function verifyHost($boolean = true) {
		curl_setopt($this->handle,CURLOPT_SSL_VERIFYHOST, $boolean); 		
	}
	
	public function verifyPeer($boolean = true) {
		curl_setopt($this->handle,CURLOPT_SSL_VERIFYPEER, $boolean);		
	}
	
	public function setCaInfo($path) {
		curl_setopt($this->handle, CURLOPT_CAINFO, $path);
	}
	
	public function setCaPath($path) {
		
	}


	
	
	
}
	
	
	
	function formatResponseBody($content, $contentType) {

		if(strpos($contentType,"json")) {
				if(is_array($content) || is_object($content)) {
						$out = json_encode($content);
				}
				else {
						$out = json_encode(array("content" => $content));
				}  
		}
		else {
				$out = $content;

		}

		return $out;
	}