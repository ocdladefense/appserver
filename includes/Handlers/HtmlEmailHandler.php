<?php

class HtmlEmailHandler extends Handler {

	
	public function __construct($output) {

		$this->output = $output;
	}
	

	
	public function getOutput() {
		
		return $this->output->getBody();
	}
	
	
	public function getHeaders() {

		return $this->output->getHeaders();
	}
}