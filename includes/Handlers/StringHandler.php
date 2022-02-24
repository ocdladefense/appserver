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
class StringHandler extends Handler {


	protected $contentTypes = array(
		"text/plain", "text/html", "application/json", "text/html+partial"
	);

	
	public function __construct($output, $contentType) {

		$this->output = $output;
		
		$this->contentType = $contentType;
	}
	
	


	public function getTexthtml() {


		// Init the theme, first.
		$className = ucfirst(strtolower(get_theme())) . "Theme";
		require(get_theme_path() . "/" . $className . ".php");
		
		$theme = new $className();
		
		$content = Template::isTemplate($this->output) ? $theme->renderTemplate($this->output) : $this->output;

		// var_dump($this->output, $content);exit;
		// Loads an HTML page with defined scripts, css.
		return $theme->render($content);
	}



	public function getTextHtmlPartial() {

		return $this->output;
	}


	public function getApplicationJson() {


	}



	public function getApplicationXml() {


	}
	

	public function getHeaders() {

      return new HttpHeader("Content-Type", "text/html");
	}
}