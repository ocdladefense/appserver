<?php

class Router {




	//An array of all the routes in all the modules using this format
	// $route["module"]
	// $route["method"]
	// $route["content-type"]
	// $route["callback"]
	// $route["files"]

	public function __construct() {
	
	}


	/**
	 * Returns a route structure
	 *  or false if no match was found.
	 *
	 */
	public function match($path, $sources){
	

		$url = Url::fromString($path);

		$sources = is_array($sources) ? $sources : array($sources);
		// Pass the selected route and any named parameters to the Route constructor.
		// return new Route($this->getFoundRoute());
		$target = $this->getTargetPath($url->getUrl(), $sources);
		
		return $target;
	}




	/**
	 * @param, request - string representing the requested URL.
	 *
	 * @param, array<String> or array<Path>
	 *
	 * @return a Path object
	 */
	function getTargetPath($request, $sources) {

		$found = false;
	
		// Pop items off the end of the $current_path 
		// $routes = array_keys($menu_items);
		$all_patterns = array();
		$possibles = array();
	
		// Locate the correct router item
		// convert each router path to a regular expression
		// and test it against the current path	
		foreach($sources as $source) {
			$p = is_object($source) && get_class($source) == "Path" ? $source : new Path($source);
			if($p->matches($request)) {
				$found = true;
				$possibles[] = $route;
				break;
			}
		}
	
		return $found ? $p: false;
	}



	/**
	 * @legacy code
	 *
	function getClickpdxRoute($path) {
		if(empty($path)) return new Clickpdx\Core\Routing\Route();
		global $menu_items;
	
		$this->getTargetPath($path, $menu_items);
	
		$router = new Clickpdx\Core\Routing\Route(
			$route,
			$path,
			$menu_items[$route]);
		
		
		return $router;
	}
	*/

	


}