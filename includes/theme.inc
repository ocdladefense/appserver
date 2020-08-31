<?php

function get_theme(){
	return ACTIVE_THEME;
}

function get_theme_path(){
	return BASE_PATH ."/content/themes/".ACTIVE_THEME;
}

// function add_script($