<?php

class HtmlEmailHandler extends Handler {

	
	public function __construct($output) {

		$this->output = $output;
	}
	


	public function getOutput($contentType = "text/html") {
		
		return $this->output->getBody();
	}
	
	
	public function getHeaders($mime = "1.0") {

		return $this->output->getHeaders();
	}
}