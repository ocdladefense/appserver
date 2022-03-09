<?php
use \Http\HttpHeader as HttpHeader;
use \File\File as File;

/**
 * Handler to return a well-formed XHTML document.
 *
 * Assume that this document handler might have some relationship to the Theme classes.
 *  For now this relationship is hard-coded; however, we should probably consider that the XHTML
 *  document itself wouldn't necessarily need any styling information.
 *  And that the theme could "inject" its scripting and styling information?
 */
class HttpResponseHandler extends Handler {


	
	public function __construct($resp, $contentType) {
		$this->output = $resp;
	}
	



	public function getTextHtml($contentType = "foobar") {

		// Theme is now initialized in Application.php.
		global $theme;

		$body = $this->output->getBody();
	
		// Loads an HTML page with defined scripts, css.
		return $theme->render($body);
	}




	public function getApplicationBase64($contentType) {

		$file = new File("OCDLA Job Description");
		$file->setContent($this->output->getBody());
		$file->setType($this->output->getHeader("Content-Type"));
		
		return $file;
	}

	public function getTextHtmlHeaders() {

		return new HttpHeader("Content-Type","text/html");
	}
	
	
	/**
	 * @TODO - Figure out how to name the file.
	 
	public function getHeaders() {
		$fileName = "OCDLA Job Description"; // $this->output->getName();

		$contentType = $this->output->getHeader("Content-Type");
	
		return array(
				new HttpHeader("Cache-Control", "private"),
				new HttpHeader("Content-Description", "File Transfer"),
				new HttpHeader("Content-Disposition", "attachment; filename=$fileName"),
				new HttpHeader("Content-Type", $contentType)
		);
	}
*/
}