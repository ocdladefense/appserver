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

	protected static $secondary_links = array();

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

	public function getScripts() {
		return $this->scripts;
	}

	public function getStyles() {

		return $this->styles;
	}
	
	
	public function addStyles($styles) {
		$this->styles = array_merge($this->styles,$styles);
		$testStyles = $this->styles;
	}
	
	public function addStyle($css) {
		$this->styles []= $css;
	}


	public static function addLinks(array $links) {

		self::$secondary_links = array_merge(self::$secondary_links, $links);
	}
	
	public static function addLink($link) {

		self::$secondary_links[] = $link;
	}

	
	public function addSearchPath($path) {
		self::$paths []= $path;
	}
	
	


	/**
	 * Load a template by name; also a shortcut to get the scripts/styles, too.
	 */
	public function render($content) {

		global $theme;

		
		$page = new Template("page");

		$body = $page->render(array(
			"content" 			=> $content,
			"theme" 			=> &$theme,
			"secondary_links"	=> self::$secondary_links
		));
		
		$template = new Template("html");


		// We have to deliberately parse the body in a separate templte file away from the scripts!
		return $template->render(array(
			"content" 	=> $body,
			"scripts" 	=> self::pageScripts($this->scripts),
			"styles" 	=> self::pageStyles($this->styles)
		));
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


	/**
	 * Helper function to format list of links into HTML <a> elements.
	 */
	function pageLinks($links = array() ) {

		// Only include active styles.
		$fn = function($link) {
			return $link["active"] !== false;
		};

		$active = array_filter($links,$fn);


		return implode("\n",array_map("Html\HtmlA",$active));
	}
}



