<?php

namespace Http;

	class CurlConfiguration {
	
	
		private $configs;
		
		
		public function __construct($config) {
			$this->configs = array_merge(self::$defaults,$config);
		}
	
		public function getAsCurl() {

			$tmp = array();
			
			foreach($this->configs as $opt => $value) {
				$tmp["CURLOPT_".strtoupper($opt)] = $value;
			}
			
			return $tmp;
		}
		
		
		public function setMethod($method = "GET") {
			if( "POST" == $method ) {
				$this->setPost();
			} elseif ( "GET" == $method ) {
				$this->setGet();
			} else {
				$this->configs["customrequest"] = $method;
			}
		}
		
		public function setBody($body) {
			$this->configs["postfields"] = $body;
		}
		
		public function setPost() {
			$this->configs["post"] = true;
		}
		
		public function setGet() {
			$this->configs["httpget"] = true;
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

		public function userAgent($ua) {
			$this->configs["useragent"] = $ua;
		}
		
		public function setHeaders($headers) {
			$this->configs["httpheader"] = $headers;
		}



		public function setCaPath($path) {}
		
		private static $defaults = array(
			"cainfo" => null,
			// "verbose" => false,
			// "stderr" => null,
			// "encoding" => '',
			"returntransfer" => true,
			// "httpheader" => null,
			"useragent" => "Mozilla/5.0",
			// "header" => 1,
			// "header_out" => true,
			"followlocation" => true,
			"ssl_verifyhost" => true,
			"ssl_verifypeer" => true
		);
		
		
		
		
	
	}