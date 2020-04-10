<?php
class DbSelectResult extends DbResult implements IDbResult, IteratorAggregate{
    //class DbSelectResult extends DbResult implements IDbResult, IteratorAggregate{
        //call the parent

    private $result;
    public $rows = [];

    public function __construct($mysqliResult){
        $this->result = $mysqliResult;
    }

    public function getIterator(){
        if($this->result->num_rows > 0){
            while($row = $this->result->fetch_assoc()){
                $this -> rows[] = $row;
            }
        }

        return new ArrayObject($this -> rows);
    }



    public function hasError(){}
    public function getError(){}
}

//the DbInsertResult class returns the id of the rows inserted
//retrun the number of rows count
//