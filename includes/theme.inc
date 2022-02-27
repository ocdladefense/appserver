<?php


// The active theme or default theme will be used.
// Let modules / routes override their theme preference.
$custom_theme = null;


function component($name, $params) {

	global $theme;

	
    // Load the widget details...
    global $presentation;

    $widget = new Presentation\Component($name,$params);
    $css = $widget->getStyles();
    $js = $widget->getScripts();
    
	// If we're not in a theme context
	// then don't add the css or scripts.
	if(null != $theme) {
		$theme->addScript($js);
		$theme->addStyle($css);
	}
	
    print $widget->toHtml(); 
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

function load_template($filename,$params = array()){
	extract($params);
	include(get_theme_path().DIRECTORY_SEPARATOR.$filename.".tpl.php"); 
}

function load_widget($filename){
	include(get_theme_path().DIRECTORY_SEPARATOR.$filename.".tpl.php"); 
}

function html($name, $data) {

	return \Html\element($name, $data);
}

// function add_script($