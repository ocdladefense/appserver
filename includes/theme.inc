<?php


// The active theme or default theme will be used.
// Let modules / routes override their theme preference.
$custom_theme = null;

// Should set this at the theme engine level.
// Should include dot prefix.
DEFINE("TEMPLATE_EXTENSION", ".tpl.php");


function component($name, $id = "", $params = array()) {

	global $theme;


	$widget = null;
	$html = "";

	try {
    	$widget = Presentation\Component::fromName($name,$id, $params);
		if(!$widget->active()) return "";

		$css = $widget->getStyles();
		$js = $widget->getScripts();
		
		// If we're not in a theme context
		// then don't add the css or scripts.
		if(null != $theme) {
			$theme->addScript($js);
			$theme->addStyle($css);
		}
		
		$html = $widget->toHtml(); 
	} catch(Presentation\ComponentException $e) {
		
		$html = "<!-- {$e->getMessage()} -->";
	}

	print $html;
}



function get_theme() {
	global $custom_theme;
	return null != $custom_theme ? $custom_theme : ACTIVE_THEME;
}



function set_theme($themeName) {
	$GLOBALS["custom_theme"] = $themeName;
	// global $custom_theme;
}



function get_theme_path() {
	return BASE_PATH ."/content/themes/" . strtolower(get_theme());
}


/**
 * @function load_template
 * 
 * Load a template by name; optionally inject params
 * into the template's scope.
 * 
 * @param $template The name of the template, not including any extensions.
 * NOTE that load_template might look in several locations to find the specified
 * template.
 * 
 * @param $vars An associative array of variables to be injected into
 * the template's scope. 
 */
function load_template($template, $vars = array()) {

	$path = get_identifier_path($template);

	extract($vars);

	$found = include($path);

	if(!$found) return false;
}


function module_template($template, $dir, $record) {

	$path = $dir . "/" . $template . TEMPLATE_EXTENSION;

	// call preprocess function on record.
	$vars = recordPreprocess($record);

	extract($vars);

	$found = include($path);

	if(!$found) return false;
}




/**
 * @function get_identifier_path
 * 
 * Return the full filesystem path for the given identifier.  Locations might be specified as either "core", "vendor", "theme", "component", or "module."
 * 
 * @param $identifier The name of symbol for which to return a filesystem path.
 * 
 * @param $location The general location of where to search for identifier.
 * 
 * @param $options Any additional options to be used when formatting the filesystem path. 
 */
function get_identifier_path($identifier, $location = "theme", $options = array()) {

	switch($location) {
		case "theme":
			$format = get_theme_path() . DIRECTORY_SEPARATOR . "%s" . TEMPLATE_EXTENSION;

		case "module":
			$format = get_module_path() . DIRECTORY_SEPARATOR . "%s" . TEMPLATE_EXTENSION;
		
		default:
			$format = get_theme_path() . DIRECTORY_SEPARATOR . "%s" . TEMPLATE_EXTENSION;
	}
	
	return sprintf($format, $identifier);
}



function html($name, $data) {

	return \Html\element($name, $data);
}