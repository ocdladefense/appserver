<?php



class WebconsoleTheme extends Template {

	
	// protected $name;
    

	
	
	public function __construct($name) {
		parent::__construct($name);
	}


	
	

	

	public function moduleGetStyles() {
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


	public function moduleGetScripts() {
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
			"$module_path/public/app.js",
		);
	
		return $scripts;
	}


	
}