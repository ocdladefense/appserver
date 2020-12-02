<?php

namespace Database;




class SObject implements \Http\IJson {

	protected $id = null;
	
	protected $type;
	
	protected $result = null;
	
	
	protected function __construct($type,$id) {
		$this->type = $type;
		$this->id = $id;
		$this->result = $this->load($id);
	}
	
	private function load($id) {
		$results = MysqlDatabase::query("SELECT * FROM {$this->type} WHERE id = {$this->id}");

		foreach($results as $row) {
				return $row;
		}
		
	}
	
	public function toJson() {
		return json_encode($this->result);
	}
}
