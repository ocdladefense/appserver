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

	public function getBody(){

		return $this->body;
	}

	public function getHeaders(){

		return $this->headers;
	}
}