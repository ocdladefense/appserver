<?php

class DbSelectResult extends DbResult implements IDbResult, IteratorAggregate {


    private $result;
    
    
    public $rows = [];
    
    

    public function __construct($mysqliResult){
        $this->result = $mysqliResult;
    }

    public function getIterator(){
        if($this->result->num_rows > 0){
            while($row = $this->result->fetch_assoc()){
                $this->rows[] = $row;
            }
        }

        return new ArrayObject($this->rows);
    }

	
		public function each($func) {
		
				$arr = array();
				foreach($this as $result) {
					$arr[] = $func($result);
				}
				
				return $arr;
		}

    public function hasError(){}
    public function getError(){}
}

