<?php



/**
 * @class Path
 *
 * @descripton, Represents
 *   a path that is either a path definition for receiving
 *   requests, or represents a requested path.
 *  Paths can contain a simplified wildcard syntax
 *   e.g., /path/to/object/%name
 *
 *  In the future paths might also contain full-fledged regular expressions
 *   that could be used in matching.
 */
class Path {

	private $path;
	
	private $patterns = array('/\//mis','/%\w+/mis');

	private $replacements = array('\/','([\w\-\s\%\'\"+_\.]+)');
	
	public function __construct($path) {
		$this->path = $path;
	}
	
	public function getValue() {
		return $this->path;
	}
	
	/**
	 * Convert a path to a regular expression.
	 *
	 */
	function toRegex() {

	
		return "/^".preg_replace( $this->patterns, $this->replacements, $this->path ) . "$/mis";
	}
	
	
	/**
	 * @method matches
	 *
	 * @description 
	 *
	 * @return boolean
	 *  true, if the string param matches.
	 *  false, if the strin param does not match.
	 */
	public function matches($str) {
			$pattern = $this->toRegex();
			if(is_object($str) && is_subclass_of($str) === "Url") {
				$str = $str->getPath();
			}
			
			return preg_match($pattern, $str, $matches) === 1;
	}

}


