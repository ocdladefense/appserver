<?php
use \Http\HttpHeader as HttpHeader;

class JsonHandler extends Handler {


	public function __construct($output, $contentType) {
		$this->output = $output;
		
		$this->contentType = $contentType;
	}
	
	
	public function getOutput($contentType = "application/json") {
			if(gettype($this->output) == "object" && in_array( "Http\IJson",class_implements($this->output))){
					return json_encode($this->output->toJson());
			} else {
					return json_encode($this->output);
			}
	}
	
	public function getHeaders($mime = "application/json") {
		return new HttpHeader("Content-Type","application/json");
	}
}