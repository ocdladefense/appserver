<?php
class DbSelectResult implements IDbResult, IteratorAggregate{
    //class DbSelectResult extends DbResult implements IDbResult, IteratorAggregate{

    private $result;

    function __construct($mysqliResult){
        $this->result = $mysqliResult;
    }

    function getIterator(){
        $rows = array();

        if($this->result->num_rows > 0){
            while($row = $this->result->fetch_assoc()){
                $rows[] = $row;
            }
        }

        return $rows;
    }

    public function hasError(){}
    public function getError(){}
}

//the DbInsertResult class returns the id of the rows inserted