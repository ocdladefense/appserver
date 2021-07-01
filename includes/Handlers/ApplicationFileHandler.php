<?php
use \Http\HttpHeader as HttpHeader;


/**
 * Handler to return a well-formed XHTML document.
 *
 * Assume that this document handler might have some relationship to the Theme classes.
 *  For now this relationship is hard-coded; however, we should probably consider that the XHTML
 *  document itself wouldn't necessarily need any styling information.
 *  And that the theme could "inject" its scripting and styling information?
 */
class ApplicationFileHandler extends Handler {


	
	public function __construct($output, $contentType) {
		$this->output = $output;
		
		$this->contentType = $contentType;
	}
	

	
	public function getOutput() {

		return $this->output;
	}
	
	public function getHeaders() {
		$fileName = $this->output->getName();
		$contentType = $this->output->getType();
	
		return array(
				new HttpHeader("Cache-Control", "private"),
				new HttpHeader("Content-Description", "File Transfer"),
				new HttpHeader("Content-Disposition", "attachment; filename=$fileName"),
				new HttpHeader("Content-Type", $contentType)
		);
	}
}