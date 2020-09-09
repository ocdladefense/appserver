<?php


class BasicTheme extends Theme {


	protected $scripts = array();
	

	
	public function __construct() {
		parent::__construct("basic");
		
		// $this->addScripts($this->getThemeScripts());
		// $this->addStyles($this->getThemeStyles());
	}


	public function addContent($content = '') {
		$this->content = $content;
	}



	/**
	 * Load a template by name; also a shortcut to get the scripts/styles, too.
	 */
	public function render($content) {

		$template = new Template("html");

		return $template->render(array(
			"content" => $content,
			"scripts" => self::pageScripts($this->scripts),
			"styles" => ''//self::pageStyles($this->styles)
			)
		);
	}






	public function getThemeStyles() {
	
		$path = "/content/themes/default";
		
		
		$styles = array(
			array(
				"active" => true,
				"rel" => "stylesheet",
				"href" => "https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css",
				"integrity" => "sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh",
				"crossorigin" => "anonymous"
			),
			array(
				"active" => true,
				"href" => $path . "/css/resets.css?bust=001"
			),
			array(
				"active" => true,
				"href" => $path . "/css/structure.css?bust=001"
			),
			array(
				"active" => true,
				"href" => $path . "/css/ux.css?bust=001"
			),
			array(
				"active" => true,
				"href" => $path . "/css/responsive.css?bust=001"
			),
			array(
				"active" => true,
				"href" => $path . "/css/menu.css?bust=001"
			),
			array(
				"active" => true,
				"href" => $path . "/css/main-menu.css?bust=001"
			),
			array(
				"active" => true,
				"href" => $path . "/css/modal.css?bust=001"
			)
		);
	
		return $styles;
	}
	
}