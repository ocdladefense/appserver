<?php
class DbInsertResult extends DbResult implements IDbResult, IteratorAggregate{

    private $result;
    private $sql;
    private $connection;

    public function __construct($mysqliResult,$sql,$connection){
        $this->result = $mysqliResult;
        $this->sql = $sql;
        $this->connection = $connection;
    }

            //what is the relationship between id and count

        // $id = mysqli_insert_id($this->connection);
        // $count = mysqli_affected_rows($this->connection);
        //set autoincrement and number of rows affected here

    public function doStuff(){
        if (!$this->hasError()) {
            $this->result = "<br><strong>New record created successfully at row" . $id . "<br></strong>";
        } else {
            $this->result = $this->getError();
        }
        return $this->result;
    }

    private function hasError(){
        if($this->result === true){
            return false;
        }
        return true;
    }

    private function getError(){
        return "<br><strong>ERROR CREATING RECORD: <br>" . $this->sql . "<br>" . $this->connection->error . "<br></strong>";
    }

    public function getIterator(){}
}