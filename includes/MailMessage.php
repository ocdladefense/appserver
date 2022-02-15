<?php





class MailMessage {


	public $body;


	public $headers;
	

	public function __costruct(){}



	public function setBody($body){

		$this->body = $body;
	}
	
	public function setHeaders($headers){

		$this->headers = $headers;
	}


	public function getBody($format = false){

		return str_replace("\n", "\r\n", $this->body);
	}


	public function getHeaders($format = false){

		return implode("\r\n", $this->headers->getHeadersAsArray());
	}
}