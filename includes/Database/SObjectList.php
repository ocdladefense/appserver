<?php




class SObjectList {
	
	private $results = array();
	
	private $name;
	
	public function __construct($object,$results = array()) {
		foreach($results as $row) {
				$this->results[] = $row;
		}
	}
	
	public function get($index) {
		return $this->results[$index];
	}
	
	
}