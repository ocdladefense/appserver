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
		"text/html", "application/json", "text/partial+html","text/plain","applicationi/xml"
	);

	
	public function __construct($output, $contentType) {

		$this->output = $output;
		
		$this->contentType = $contentType;
	}


	public function getTextHtml($params = array()) {
		global $theme;


		
		$content = Template::isTemplate($this->output) ? $theme->renderTemplate($this->output) : $this->output;;

		// var_dump($this->output, $content);exit;
		// Loads an HTML page with defined scripts, css.
		return $theme->render($content);
	}



	public function getTextPartialHtml() {

		return $this->output;
	}


	public function getApplicationJson() {
		$out = new stdClass();
		$out->errors = false;
		$out->content = $this->output;

		return json_encode($out);
	}



	public function getApplicationXml() {
		
		return $this->output;
	}
	

	public function getTextPartialHtmlHeaders(){

		return new HttpHeader("Content-Type", "text/html");
	}

	public function getTextHtmlHeaders($params = array()) {
		// var_dump($params);exit;
		$type = array("text/html");
		$parts = array_merge($type, $params);
		// var_dump($parts);exit;
		$value = implode(";", $parts);

      	return new HttpHeader("Content-Type", $value);
	}

	public function getApplicationXmlHeaders() {

		return new HttpHeader("Content-Type", "application/xml");
	  }

	  public function getApplicationJsonHeaders() {

		return new HttpHeader("Content-Type", "application/json");
	  }
}