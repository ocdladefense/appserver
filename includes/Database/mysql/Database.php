<?php

namespace Mysql;

use DbInsertResult;
use DbDeleteResult;
use DbUpdateResult;
use DbException;


class Database {

    private $connection;

    function __construct($alias = null){
        $this->connect($alias);
    }

    function connect($credentials = array()){

        $this->connection = new \Mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

        if ($this->connection->connect_error) die("Connection failed: " . $this->connection->connect_error);
    }

    function insert($sql){

        $result = $this->connection->query($sql);
        if($result !== true) throw new DbException("Error inserting data.  " . $this->connection->error);

        $count = mysqli_affected_rows($this->connection);
        if($count == 0) throw new DbException("There were ". $count . " rows inserted.");

        $id = mysqli_insert_id($this->connection);
        if($id === null || $id == 0 || $id == "") throw new DbException("The given id cannot be null or equal to 0 or an empty string");

        return new DbInsertResult($result,$id,$count,$this->connection->error);
    }

    function update($sql){

        $result = $this->connection->query($sql);
        if($result !== true) throw new DbException("Error updating data.  " . $this->connection->error);

        $count = mysqli_affected_rows($this->connection);
        if($count == 0) throw new DbException("There were ". $count . " rows updated.");

        return new DbUpdateResult($result,$count,$this->connection->error);
    }
    
    function delete($sql){

        $result = $this->connection->query($sql);
        if($result !== true) throw new DbException("Error deleting data.  " . $this->connection->error);

        $count = mysqli_affected_rows($this->connection);
        if($count == 0) throw new DbException("There were ". $count . " rows deleted.");

        return new DbDeleteResult($result,$count,$this->connection->error);
    }

    function select($sql){

        $result = $this->connection->query($sql);
        if(!$result) throw new DbException($this->connection->error);

        return new \DbSelectResult($result);
    }
    
    public static function query($sql, $type = "select"){

        $db = new Database();

        switch($type) {
            case "select":
                return $db->select($sql);
                break;
            case "insert":
                return $db->insert($sql);
                break;
            case "update":
                return $db->update($sql);
                break;
            case "delete":
                return $db->delete($sql);
                break;
        }      
    }
    
    function close(){
        $this->connection->close();
    }
    
    public static function getSelectList($field, $table) {
            $dbResults = MysqlDatabase::query("SELECT DISTINCT {$field} FROM {$table} ORDER BY {$field}");
            $parsedResults = array();
            foreach($dbResults as $result) {
                    $parsedResults[] = $result[$field];
            }
            return $parsedResults;
    }
}
