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
	
	public static function renderTemplate($name,$vars) {
		$template = new Template($name);
		
		return $template->render($vars);
	}
	
	public static function pathToTemplate($name){
		return get_theme_path() ."/".$name.self::$TEMPLATE_EXTENSION;
	}
	
	
	public static function exists($name){
		return file_exists(self::pathToTemplate($name));
	}
	
	
	

	public static function moduleGetStyles() {
		$styles = array(
			array(
				"active" => false,
				"rel" => "stylesheet",
				"href" => "https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css",
				"integrity" => "sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh",
				"crossorigin" => "anonymous"
			),
			array(
				"active" => true,
				"href" => "/modules/webconsole/assets/ux/structure.css"
			),
			array(
				"active" => true,
				"href" => "/modules/webconsole/assets/ux/ux.css"
			),
			array(
				"active" => true,
				"href" => "/modules/webconsole/assets/ux/responsive.css"
			),
			array(
				"active" => true,
				"href" => "/modules/webconsole/assets/ux/menu.css"
			),
			array(
				"active" => true,
				"href" => "/modules/webconsole/assets/ux/modal.css"
			),
			array(
				"active" => false,
				"href=" => "/modules/webconsole/modules/material/style.css"
			),
			array(
				"active" => false,
				"href" => "/modules/webconsole/assets/css/KeyboardManager.css"
			),
			array(
				"active" => true,
				"href" => "/modules/webconsole/modules/note/style.css"
			),
			array(
				"active" => true,
				"href" => "/modules/webconsole/assets/css/siteStatus.css"
			),
			array(
				"active" => true,
				"href" => "/modules/webconsole/modules/modal/style.css"
			),
			array(
				"active" => true,
				"href" => "/modules/webconsole/modules/ors/style.css"
			)
		);
	
		return $styles;
	}


	public static function moduleGetScripts() {
		$module_path = "/modules/webconsole";

		$scripts = array(
			"$module_path/assets/lib/event.js",
			"$module_path/assets/lib/datetime.js",
			"$module_path/assets/lib/modal.js",
			"$module_path/assets/lib/view.js",
			"$module_path/assets/lib/Dom.js",
			"$module_path/assets/lib/http/http.js",
			"$module_path/assets/lib/http/HttpCache.js",
			"$module_path/assets/lib/KeyboardManager.js",
			"$module_path/assets/lib/database/Database.js",
			"$module_path/assets/lib/database/DatabaseArray.js",
			"$module_path/assets/lib/database/DatabaseIndexedDb.js",
			"$module_path/assets/lib/Client.js",
			"$module_path/assets/lib/UrlParser.js",

			"$module_path/assets/event/DomDataEvent.js",
			"$module_path/assets/event/DomLayoutEvent.js",
			"$module_path/assets/event/DomHighlightEvent.js",
			"$module_path/assets/event/DomMobileContextMenuEvent.js",


		
			/*
			"modules/document/src/TableOfContents.js",
			"modules/document/src/Doc.js",
			"modules/document/route.js",
			*/
		
			"$module_path/modules/editable/DomEditableEvent.js",
			"$module_path/modules/editable/DomContextMenuEvent.js",

		
			"$module_path/modules/linkHandler/src/LinkHandler.js",

			"$module_path/modules/modal/component/ModalComponent.js",
			"$module_path/modules/modal/src/Modal.js",
			"$module_path/modules/modal/src/PositionedModal.js",

			"$module_path/modules/note/component.js",
			"$module_path/modules/note/route.js",
			"$module_path/modules/note/src/Note.js",
		
			"$module_path/modules/material/component.js",

			"$module_path/modules/audio/src/DomAudio.js",

			"$module_path/routes.js",
			"$module_path/assets/ux/ui.js",
			"$module_path/assets/ux/menu.js",
		
			"$module_path/settings.js",
			"$module_path/public/app.js"
		);
	
		return $scripts;
	}



	/**
	 * Load a template by name; also a shortcut to get the scripts/styles, too.
	 */
	public static function loadTemplate($name) {
		$template = new Template($name);

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
		$template->addScripts(self::moduleGetScripts());
		$template->addStyles(self::moduleGetStyles());
	
	
		return $template;
	}
	
	
	
}