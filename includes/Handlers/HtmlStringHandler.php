<?php
use \Http\HttpHeader as HttpHeader;

class HtmlStringHandler extends Handler {


	
	public function __construct($output, $contentType) {
		$this->output = $output;
		
		$this->contentType = $contentType;
	}
	
	
	public function getOutput() {
		// Loads an HTML page with defined scripts, css.
		return $this->output;
	}
	
	public function getHeaders() {

      return new HttpHeader("Content-Type", "text/html");
	}
}