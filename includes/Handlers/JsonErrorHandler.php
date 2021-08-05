<?php
use \Http\HttpHeader as HttpHeader;

class JsonErrorHandler extends Handler {

	private $message;
	private $stack;

	public function __construct($e, $contentType) {
		$this->output = $e;
		$this->message = $e->getMessage();
		$this->stack = $e->getTrace();
		
		$this->contentType = $contentType;
	}
	
	
	public function getOutput() {
			// Loads an HTML page with defined scripts, css.
			return json_encode(array(
				
				"error" => $this->message,
				"stack" => $this->stack
			));
	}
	
	public function getHeaders() {
		return new HttpHeader("Content-Type","application/json");
	}

	public function removeStack() {

		$this->stack = array();
	}
}