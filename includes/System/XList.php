<?php


class XList {

	protected $items = array();



	public function __construct($items = array()) {
		$this->items = $items;
	}



	public function getArray() {
		return $this->items;
	}

	public function put($index, $value) {

		$this->items[$index] = $value;

	}



	public static function fromFilesystem($startPath = null) {
		

		$items = array();
		
		$previous = getcwd();
		chdir($startPath);


		$files = scandir(".");

		foreach($files as $dir)  {
				if(!is_dir($dir) || $dir == ".." || $dir == ".")
				continue;
				$items[] = $startPath . "/" . $dir;
		}
		chdir($previous);
		
		return new XList($items);
	}




	
	
	
	public function map($fn = null, $preserveKeys = true) {
		if(null == $fn) return $this;
		
		return new XList(array_map($fn,$this->items));
	}
	
	
	public function filter($fn = null) {
		if(null == $fn) return $this;
		
		$return = array();
		foreach($this->items as $key => $item) {
			if(false === $fn($item,$key)) continue;
			$return[$key] = $item;
		}
		
		return new XList($return);
	}
	
	
	public function flatten() {
		$flatten = array();
		foreach($this->items as $key => $value) {
			$flatten += $value;
		}
		
		return $flatten;
	}
	
	
	public function indexBy($fn = null) {
		
		$index = array();
		
		foreach($this->items as $item) {
			$keys = $fn($item);
			$keys = is_array($keys) ? $keys : array($keys);
			if(empty($keys)) continue;

			
			foreach($keys as $key) {
				$index[$key] = $item;
			}
		}
		
		return new XIndex($index);
	}

}






class XIndex extends XList {


	
	public function __construct($items) {
		parent::__construct($items);
	}
	



	public function getAll($xpath) {
		$compilation = array();
		foreach($this->items as $key => $def) {
			$routes = empty($def[$path]) ? array() : $def[$path];
			$compilation = array_merge($compilation, $routes);
		}
		
		return $compilation;
	}
	
	

}