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

function createDataList($name, $values){

	$children = array_map(function($judge){

		return array("name" => "option", "attrs" => array(), "children" => $judge);

	}, $values);

	return createElement("datalist", array("name" => $name, "id" => $name), $children);
}

function createElement($tagName, $attrs, $children = null){

	// Not all tags support all attributes.
	$openTag = "<$tagName";

	foreach($attrs as $key => $value) {

		$openTag .= " $key='$value'";
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

