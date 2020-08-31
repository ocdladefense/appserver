<?php


class JsonHandler extends Handler {


	public function __construct($output, $contentType) {
		$this->output = $output;
		
		$this->contentType = $contentType;
	}
	
	
	public function getOutput() {
			if(gettype($this->output) == "object" && in_array( "Http\IJson",class_implements($this->output))){
					return $this->output->toJson();
			} else {
					return json_encode($this->output);
			}
	}
}