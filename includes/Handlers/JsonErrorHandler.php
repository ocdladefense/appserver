<?php
use \Http\HttpHeader as HttpHeader;

class JsonErrorHandler extends Handler {

	public function __construct(Exception $e, $contentType) {
		$this->output = $e;
		
		$this->contentType = $contentType;
	}
	
	
	public function getOutput() {
			// Loads an HTML page with defined scripts, css.
			return array(
				"error" => $this->output->getMessage()
			);
	}
	
	public function getHeaders() {
		return new HttpHeader("Content-Type","application/json");
	}
}