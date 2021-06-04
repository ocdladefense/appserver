<?php
class DbInsertResult extends DbResult implements IDbResult, IteratorAggregate{

    private $result;
    private $sql;
    private $id;
    private $count;
    private $error;

    public function __construct($mysqliResult,$id,$count = 0,$error = ""){
        $this->result = $mysqliResult;
        $this->id = $id;
        $this->count = $count;
        $this->error = $error;
    }

            //what is the relationship between id and count
        //set autoincrement and number of rows affected here

    public function getResult(){

        if (!$this->hasError()) {
            $this->result = "<br><strong>New record or records created successfully starting at row " . $this->id .".  There were ". $this->count . " rows added to database.<br></strong>";
        } else {
            $this->result = $this->getError();
        }
    }

    private function hasError(){
        if($this->result === true){
            return false;
        }
        return true;
    }

    private function getError(){
        return "<br><strong>ERROR CREATING RECORD: <br>" ."<br>" . $this->connection->error . "<br></strong>";
    }

    public function getIterator(){
        $ids = array($this->id);

        for($i = 1; $i < $this->count; $i++){
            $nextId = $this->id + $i;

            $ids[] = $nextId;
        }
        return new ArrayObject($ids);
    }

    public function getId(){

        return $this->id;
    }
}