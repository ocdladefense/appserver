<?php

/**
 * Helper function to format list of styles into HTML <link> elements.
 */
function pageStyles($styles = array() ) {

	// Only include active styles.
	$fn = function($style) {
		return $style["active"] !== false;
	};

	$active = array_filter($styles,$fn);


	return array_map("Html\HtmlLink",$active);
}



/**
 * Helpfer function to format list of scripts into HTML <script> elements.
 */
function pageScripts($scripts = array() ) {
	return array_map("Html\HtmlScript",$scripts);
}



class Template
{
    
	private static $TEMPLATE_EXTENSION = ".tpl.php";
	
	private $name;
    
	private $path;
	
	private $scripts = array();

	private $styles = array();

	private $footerScripts;



	public function __construct($name){
		$this->name = $name;
	}
		
	public function addScripts($scripts) {
		$this->scripts = array_merge($this->scripts,$scripts);
	}
	
	public function addStyles($styles) {
		$this->styles = array_merge($this->styles,$styles);
	}

	public function render($context = array()){
		if(!self::exists($this->name)){
			throw new \Exception("TEMPLATE_ERROR: Template {$this->pathToTemplate()} could not be found.");
		}
		
		$context["styles"] = implode("\n",pageStyles($this->styles));
		$context["scripts"] = implode("\n",pageScripts($this->scripts));
		
		extract($context);
		ob_start();
		include self::pathToTemplate($this->name);
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
	
	public static function pathToTemplate($name){
		return get_theme_path() ."/".$name.self::$TEMPLATE_EXTENSION;
	}
	
	public static function exists($name){
		return file_exists(self::pathToTemplate($name));
	}
}