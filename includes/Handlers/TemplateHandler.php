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
class TemplateHandler extends Handler {

	
	public function __construct($output, $contentType) {

		$this->output = $output;
		
		$this->contentType = $contentType;
	}
	


	public function getTextHtml() {
		global $theme;


		
		$content = Template::isTemplate($this->output) ? $theme->renderTemplate($this->output) : $this->output;;

		// var_dump($this->output, $content);exit;
		// Loads an HTML page with defined scripts, css.
		return $theme->render($content);
	}
	

	public function getHeaders($mime = "text/html") {

      return new HttpHeader("Content-Type", "text/html");
	}
}