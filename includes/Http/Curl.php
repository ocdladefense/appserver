<?php

namespace Http;

class Curl {


	public static function send($url, $options = array()) {
		


		// If set, start logging the request.
		$curl_log_file = "curl_log.txt";
		$out  =  fopen($curl_log_file,"w+");
		if( !$out ) throw new Exception("Could not open PHP output stream.");

		
				
		$curl = curl_init($url);

		// Set headers and other options, too.
		foreach($options as $opt => $value) {
			curl_setopt($curl, \constant($opt), $value);
		}
		
		curl_setopt($curl, CURLOPT_STDERR, $out);
		curl_setopt($curl, CURLOPT_VERBOSE, true);




		
		// Send the request using cURL.
		$resp = curl_exec($curl);


		if(false) {
			// self::$headersSent = curl_getinfo($curl, CURLINFO_HEADER_OUT);
		}



		//@jbernal from apiclient line 313		
		//else {
		$headerOffset = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
		$headers = self::httpParseHeaders(substr($resp, 0, $headerOffset));
		$body = substr($resp, $headerOffset);
		$info = curl_getinfo($curl);


		curl_close($curl);


		fclose($out);  
		
		//this value is necessary to discard any garbage data
		$LOG_DATA_START = "Trying";

		$logData = "Trying" . explode($LOG_DATA_START, file_get_contents($curl_log_file))[1];

		




		return array(
			"headers" 	=> $headers,
			"body"		=> $body,
			"info"		=> $info,
			"log"		=> $logData
		);
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
	
}