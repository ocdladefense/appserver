<?php

namespace Html;

const VERSION = "5";

const DOC_TYPE = "html5";


function HtmlLink($style) {

	$elem = "<link rel='stylesheet' type='text/css' ";

	foreach($style as $prop => $val) {

		if($prop == "active") continue;

		$elem .= "{$prop}='{$val}'";
	}

	return $elem .= " />";
}


function HtmlScript($script) {

	$kvp = array();
	$elem = "<script ";
	
	if(gettype($script) === "string") $script = array("src" => $script);
	
	if(!isset($script["type"])) $script["type"] = "text/javascript";
	
	foreach($script as $prop => $val) {

		if($prop == "active") continue;

		$kvp[] = attr($prop,$val);
	}

	return $elem .= implode(" ",$kvp) .">\n</script>";
}

function Html($link) {

	$kvp = array();


	return "<a class='foobar'>Foobar</a>";
}



/**
 * Create a function that can render any HTML element.
 * 
 * We take the $name of the tag and call the relevant function, 
 *  passing in $data to fill in the element attributes (i.e., key/value pairs.)
 */
function element($name, $data) {

	switch($name) {

		case "a":
			return createAElement($data);
			break;
	}
}

function createAElement($props) {

	return sprintf("<a href='%s'>%s</a>", $props["href"], $props["label"]);
}


function attr($prop,$val = null) {

	if($val == null) return $prop;

	else return "{$prop}='{$val}'";
}





// Needs a comprehensive comment.
function DataList($name, $values){

	$options = array_map(function($value) {

		return array("name" => "option", "attrs" => array(), "children" => $value);

	}, $values);

	return createElement("datalist", array("name" => $name, "id" => $name), $options);
}




// This REQUIRES an associative array.
function Select($name, $options = array(), $selected = null){

	// Create our options array for this select element.
	$children = array_map(function($optValue, $optText) use ($selected){

		// Only valid if we are considering 
		// passing in a standard array.
		// $text = ucwords($opt); // This gets displayed to the user.


		$isSelected = strtolower($selected) == strtolower($optValue);

		$attrs = array("value" => $optValue);
		if($isSelected) {
			$attrs["selected"] = "";
		}

		return array("name" => "option", "attrs" => $attrs, "children" => $optText);

	},array_keys($options),$options);

	return createElement("select", array("name" => $name), $children);
}




function Autocomplete($name, $datalist, $value = null, $placeholder = "") {

	return "<input autocomplete='off' type='text' name='{$name}' value='{$value}' list='{$datalist}' placeholder='{$placeholder}' onchange='submitForm();' />";
}

function Input($name) {


}

function Checkbox($name, $checked = false) {

	return "<input type='checkbox' id='$name' name='{$name}' />";
}

/*

        <input autocomplete="off" type="text" name="appellate_judge" value="<?php print $appellate_judge; ?>" data-datalist="judges" placeholder="Appellate Judge" onchange="submitForm()" />

        <input autocomplete="off" type="text" name="trial_judge" value="<?php print $trial_judge; ?>" data-datalist="judge" placeholder="Trial Judge" onchange="submitForm()" />

        <input id="summarize-checkbox" class="checkbox-option filter-item" type="checkbox" <?php print $summarizeChecked; ?> name="summarize" value="1" />
*/

function Button($name, $label) {
	//<a class="filter-item" href="/car/list">Clear</a>
	return "<button name='{$name}' id='{$name}'>{$label}</button>";
}

function Date($name, $value, $max = null){

	return "<input type='date' name='$name' id='$name' value='$value' />";
}


function createElement($tagName, $attrs, $children = null){

	// Not all tags support all attributes.
	$openTag = "<$tagName";

	foreach($attrs as $key => $value) {

		$openTag .= " " . $key . "='" . $value ."'";
	}

	$openTag .= ">";


	$closeTag = "</$tagName>";

	$theTag = $openTag . $closeTag;


	if(!empty($children) && is_string($children)) {

		return $openTag . $children . $closeTag;

	} else if(!empty($children) && is_array($children)) {

		$fn = function($child){
			$name = $child["name"];
			$attrs = $child["attrs"];
			$children = $child["children"];

			return createElement($name, $attrs, $children);
		};

		return $openTag . implode("\n", array_map($fn, $children)) . $closeTag;
	}

	return $theTag;

}





class Html {

	public static function toList($items,$heading) {

		return "<h2>{$heading}</h2><ul>" . implode("\n",array_map(function($item) {
			return "<li>{$item}</li>";
		}, $items))."</ul>";
	}
	
}

