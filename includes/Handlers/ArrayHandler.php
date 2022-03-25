<?php

use \Http\HttpHeader as HttpHeader;

/**
 * Handler to return a PHP array as either text, html or json.
 *
 */
class ArrayHandler extends Handler {

	
	public function __construct($output, $contentType) {

		$this->output = $output;
		
		$this->contentType = $contentType;
	}
	


	public function getApplicationJson() {
		return json_encode($this->output);
	}
	

	public function getHeaders($mime = "application/json") {

      return new HttpHeader("Content-Type", "application/json");
	}

	public function getApplicationJsonHeaders() {

		return new HttpHeader("Content-Type", "application/json");
	  }
}