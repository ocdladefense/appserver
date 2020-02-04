<?php
class MysqlDatabase{

    private $obj;
    private $tableName;
    private $connection;

    function __construct($obj,$tableName){
        $this->obj = $obj;
        $this->tableName = $tableName;
    }

    function connect(){
                // Create connection
        $this->connection = new mysqli(SERVER_NAME,USER_NAME,PASSWORD);

        // Check connection
        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    function insert(){
        $fieldsAndValues = get_object_vars($this->obj);
        $addSlashedValues = array();
        
        foreach($fieldsAndValues as $value){
            $addSlashedValues[] = addslashes($value);
        }

        $columns = implode(", ",array_keys($fieldsAndValues));
        $values = implode("','",$addSlashedValues);

        echo "Connected successfully";
        $this->connection = new mysqli(SERVER_NAME, USER_NAME, PASSWORD, $this->tableName);
        $query = "INSERT INTO cars ($columns)
        VALUES ('$values')";

        if ($this->connection->query($query) === TRUE) {
            echo "<br><strong>New record created successfully<br></strong>";
        } else {
            echo "<br><strong>ERROR CREATING RECORD: <br>" . $query . "<br>" . $this->connection->error . "<br></strong>";
        }
    }
    
    function close(){
        $this->connection->close();
    }
}
function mysqlDatabaseInsert($obj,$tableName){
    $db = new mysqlDatabase($obj,$tableName);
	$db->connect();
	$db->insert();
	$db->close();
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
