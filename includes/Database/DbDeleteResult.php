<?php
class DbDeleteResult extends DbResult implements IDbResult, IteratorAggregate{

    private $result;
    private $count;
    private $error;
    private $rows = [];

    public function __construct($mysqliResult,$count = 0,$error = ""){
        $this->result = $mysqliResult;
        $this->count = $count;
        $this->error = $error;
    }

    public function getResult(){

        if (!$this->hasError()) {
            $this->result = "<br><strong>There were ". $this->count . " rows deleted from the database.<br></strong>";
        } else {
            $this->result = $this->getError();
        }
    }

    public function hasError(){
        if($this->result === true){
            return false;
        }
        return true;
    }

    private function getError(){
        return "<br><strong>ERROR DELETING RECORD: <br>" ."<br>" . $this->connection->error . "<br></strong>";
    }

    public function getIterator(){
        if($this->result->num_rows > 0){
            while($row = $this->result->fetch_assoc()){
                $this -> rows[] = $row;
            }
        }

        return new ArrayObject($this -> rows);
    }
}