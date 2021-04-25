<?php

class Theme {


	protected $name;
	
	/**
	 * @var $paths
	 *
	 * @description An array of paths to be prepended to $name.
	 */
	protected static $paths = array();
	
	protected $scripts = array();

	protected $styles = array();

	protected $footerScripts = array();
	

	public function __construct($name) {
		$this->name = $name;
	}
	
	
	public function addScripts($scripts) {
		$this->scripts = array_merge($this->scripts,$scripts);
	}
	
	public function addScript($js) {
		$this->scripts[] = $js;
	}
	
	public function addStyles($styles) {
		$this->styles = array_merge($this->styles,$styles);
		$testStyles = $this->styles;
	}
	
	public function addStyle($css) {
		$this->styles []= $css;
	}
	
	public function addSearchPath($path) {
		self::$paths []= $path;
	}
	
	
	/**
	 * @method renderTemplate
	 *
	 * @param Template $tpl
	 *  - Instance of a Template that can be rendered.
	 *  If the template has scripts or styles then load those
	 *  into this theme.
	 */
	public function renderTemplate(Template $tpl) {

		$this->addScripts($tpl->getScripts());
		$this->addStyles($tpl->getStyles());
		
		return $tpl->isRendered() ? $tpl->getOutput() : $tpl->render();
	}
	
	public static function pathToTemplate($name) {
		
		$paths = self::$paths;
		$paths[]= get_theme_path();
		
		$search = array_map(function($item) use($name) {
			return $item . "/" . $name . self::TEMPLATE_EXTENSION;
		},$paths);
		
		$searchPaths = implode(",", $search);


		$found = array_values(array_filter($search,function($file) {
			return file_exists($file);
		}));
		
		if(!count($found) > 0) throw new \Exception("TEMPLATE_ERROR: Template $name could not be found at: " . $searchPaths);
		
		
		return $found[0];
	}


	/**
	 * Helper function to format list of scripts into HTML <script> elements.
	 */
	function pageScripts($scripts = array() ) {
		return implode("\n",array_map("Html\HtmlScript",$scripts));
	}
	
	/**
	 * Helper function to format list of styles into HTML <link> elements.
	 */
	function pageStyles($styles = array() ) {

		// Only include active styles.
		$fn = function($style) {
			return $style["active"] !== false;
		};

		$active = array_filter($styles,$fn);


		return implode("\n",array_map("Html\HtmlLink",$active));
	}
}



