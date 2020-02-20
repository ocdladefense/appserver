<?php
class MysqlDatabase{

    private $connection;

    function __construct(){
        $this->connect();
    }

    function prepareData($data){

        $addSlashedValues = array();
        
        foreach($data as $value){
            $addSlashedValues[] = addslashes($value);
        }

        return $addSlashedValues;
    }

    function connect(){
                // Create connection
        $this->connection = new mysqli(HOST_NAME,USER_NAME,USER_PASSWORD, DATABASE_NAME);

        // Check connection
        if ($this->connection->connect_error) {
            //SHOULD THROW A DBCONNECTION ERROR
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    function insert($tableName,$columns,$values){
        $escaped = $this->prepareData($values);
        $formatted = implode("','",$escaped);
        $columnNames = implode(", ",$columns);
        $query = "INSERT INTO $tableName ($columnNames) VALUES ('$formatted')";

        $this->doQuery($query);
    }

    function doQuery($query){
        if ($this->connection->query($query) === TRUE) {
            echo "<br><strong>New record created successfully<br></strong>";
        } else {
            echo "<br><strong>ERROR CREATING RECORD: <br>" . $query . "<br>" . $this->connection->error . "<br></strong>";
        }
    }

    function delete(){}
    
    function close(){
        $this->connection->close();
    }
}

function insert($obj){
    $values = get_object_vars($obj);
    
    $columns = array_keys($values);

    $tableName = get_class($obj);

    $db = new MysqlDatabase();
	$db->insert($tableName,$columns,$values);
}