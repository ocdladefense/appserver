<?php


// The active theme or default theme will be used.
// Let modules / routes override their theme preference.
$custom_theme = null;


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

function load_template($filename){
	include(get_theme_path().DIRECTORY_SEPARATOR.$filename.".tpl.php"); 
}

function load_widget($filename){
	include(get_theme_path().DIRECTORY_SEPARATOR.$filename.".tpl.php"); 
}

// function add_script($