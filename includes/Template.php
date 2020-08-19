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
  
  const DEBUG = false;
  
	const TEMPLATE_EXTENSION = ".tpl.php";
	
	protected $name;
    
	/**
	 * @var $paths
	 *
	 * @description An array of paths to be prepended to $name.
	 */
	protected static $paths = array();
	
	protected $scripts = array();

	protected $styles = array();

	protected $footerScripts;

	protected $log = array();
	
	
	
	
	public function __construct($name){
		$this->name = $name;
	}
		
	public function addScripts($scripts) {
		$this->scripts = array_merge($this->scripts,$scripts);
	}
	
	public function addScript($js) {
		$this->scripts []= $js;
	}
	
	public function addStyles($styles) {
		$this->styles = array_merge($this->styles,$styles);
	}
	
	public function addStyle($css) {
		$this->styles []= $css;
	}


	public static function addPath($path) {
		self::$paths []= $path;
	}


	public function render($context = array()) {
		
		$context["styles"] = implode("\n",pageStyles($this->styles));
		$context["scripts"] = implode("\n",pageScripts($this->scripts));
		
		extract($context);
		ob_start();
		
		$this->log("About to include template name, {$this->name}.");
		$file = self::pathToTemplate($this->name);
		$this->log("About to include template file, {$file}.");
		include $file;
		
		$content = ob_get_contents();
		ob_end_clean();
		
		
		return $content;
	}
	
	
	
	
	public static function renderTemplate($name,$vars) {
		$template = new Template($name);
		
		return $template->render($vars);
	}
	
	
	
	
	
	public static function pathToTemplate($name) {
		
		$paths = self::$paths;
		$paths[]= get_theme_path();
		
		$search = array_map(function($item) use($name) {
			return $item . "/" . $name . self::TEMPLATE_EXTENSION;
		},$paths);


		$found = array_values(array_filter($search,function($file) {
			return file_exists($file);
		}));
		
		if(!count($found) > 0) throw new \Exception("TEMPLATE_ERROR: Template $name could not be found.");
		
		
		return $found[0];
	}
	
	
	public static function exists($name){
		return file_exists(self::pathToTemplate($name));
	}
	

	
	/**
	 * Load a template by name; also a shortcut to get the scripts/styles, too.
	 */
	public static function loadTemplate($name) {

		// Init the theme, first.
		$path = get_theme_path();
		require($path . "/Theme.php");
	
		$template = new DefaultTheme($name);


		$bootstrap = array(
			array(
				"src" => "https://code.jquery.com/jquery-3.4.1.slim.min.js",
				"integrity" => "sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n",
				"crossorigin" => "anonymous",
				"active" => true
			),
			array(
				"src" => "https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js",
				"integrity" => "sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo",
				"crossorigin" => "anonymous",
				"active" => true
			),
			array(
				"src" => "https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js",
				"integrity" => "sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6",
				"crossorigin" => "anonymous",
				"active" => true
			)
		);

		$jquery = array(
			array(
				"src" => "/modules/webconsole/assets/jquery/jquery-1.11.0-min.js"
			)
		);

	
		$react = array(
			array(
				"src" => "/modules/webconsole/assets/react/react-16.12.0-development.js"
			),
			array(
				"src" => "/modules/webconsole/assets/react/react-16.12.0-dom-development.js"
			),
			array(
				"src" => "/modules/webconsole/assets/react/babel-6.26.0-standalone.js"
			)
		);

	
		$template->addScripts($react);
		$template->addScripts($bootstrap);
		$template->addScripts($template->moduleGetScripts());
		$template->addStyles($template->moduleGetStyles());
	
	
		return $template;
	}
	
	
	// Save some log messages.
	//  Might be of interest later.
	protected function log($msg) {
		if(self::DEBUG) print $msg;
		$this->log []= $msg;
	}	
	
}