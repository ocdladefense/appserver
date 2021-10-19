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
	
	if(gettype($script) === "string") {
		$script = array(
			"src" => $script
		);
	}
	
	if(!isset($script["type"])) {
		$script["type"] = "text/javascript";
	}
	
	foreach($script as $prop => $val) {
		if($prop == "active") continue;
		$kvp[] = attr($prop,$val);
	}
	return $elem .= implode(" ",$kvp) .">\n</script>";
}


function attr($prop,$val = null) {
	if($val == null) return $prop;
	else return "{$prop}='{$val}'";
}

function createElementExperiment($tagName, $attributes, $children) {
	$attributeStrings = array();
	foreach($attributes as $key => $value) {
		$attributeStrings[] = "{$key}=\"{$value}\"";
	}
	if(is_array($children)){
		$children = implode("\n",$children);
	}
	return "<{$tagName} ".implode(" ",$attributeStrings).">{$children}</{$tagName}>";
}


function createElement($tagName, $attrs){

	$id = $attrs["id"];
	$name = $attrs["name"];
	$classes = $attrs["classes"];

	$openTag = "<$tagName id='$id' name='$name' class='$classes'>";
	$closeTag = "</$tagName>";

	$theTag = $openTag . $closeTag;

	if(empty($attrs["options"]) || ($tagName != "select" && $tagName != "datalist")){

		return $theTag;

	}

	$optionElements = array();

	$options = $attrs["options"];

	$currentlySelected = $attrs["selected"];

	foreach($options as $value => $label){

		$selected = $currentlySelected == $value ? "selected" : "";

		// You don't want to set the label if you are building options for a datalist.  Creates duplicates.
		if($tagName == "datalist") $label = null;

		$optionElements[] = "<option value='$value' $selected>$label</option>";
	}

	return $openTag . implode("", $optionElements) . $closeTag;

}



class Html {

	public static function toList($items,$heading) {
		return "<h2>{$heading}</h2><ul>" . implode("\n",array_map(function($item) {
			return "<li>{$item}</li>";
		}, $items))."</ul>";
	}
	
}

