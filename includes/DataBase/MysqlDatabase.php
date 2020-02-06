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

    function update(){}
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


// //add the url to the db
// //make sure that the prop names are the same as the column names

// // <includes>
// <MysqlDatabase class="php">
// </MysqlDatabase>

// insert someObject;
// update someObject;

// // v. 2.0 
// // It will also allo wyou to *modify column names
// registerObject($object_definition); // This function would create the table, if that's the direction you ultimately want to head in.

// dbinsert(someObject);
// function dbinsert($obj){
//     $mysql = new MysqlQuery();
//     $table = $locate the Object table;
//     $cols = array();
//     foreach(get_properties($obj) as $colName){
//         $cols [] = $colName;
//         // skip over the Id.
//     }
// }
// loop through the public properties, and consider each property a column name; the value of each property is the column value.
