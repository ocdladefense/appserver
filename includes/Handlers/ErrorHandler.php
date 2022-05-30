<?php
use \Http\HttpHeader as HttpHeader;


class ErrorHandler extends Handler {

	
	public function __construct($output, $contentType) {
		$this->output = $output;
		
		$this->contentType = $contentType;
	}
	  
	
	public function getHeaders($mime = "text/html") {

      return new HttpHeader("Content-Type", "text/html");
	}


	public function getTextHtml() {
		global $theme;

		$content = $this->output->getMessage();
		
		// Loads an HTML page with defined scripts, css.
		return $theme->render($content);
	}



	public function getApplicationJson() {
		return json_encode(array(
				
			"error" => $this->output->getMessage()
			// "stack" => $this->stack
		));
	}

	public function getApplicationJsonHeaders() {

		return new HttpHeader("Content-Type", "application/json");
	  }
}