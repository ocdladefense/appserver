<?php
use \Http\HttpHeader as HttpHeader;


class HtmlErrorHandler extends Handler {

	
	public function __construct($output, $contentType) {
		$this->output = $output;
		
		$this->contentType = $contentType;
	}
	  
	
	public function getOutput() {
			// Loads an HTML page with defined scripts, css.
			return "There was an error: ".$this->output->getMessage();
	}
	
	public function getHeaders() {

      return new HttpHeader("Content-Type", "text/html");
	}
}