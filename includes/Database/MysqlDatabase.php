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

    function doSelect($json){
        $rows = array();

        $sql = selectClause().whereClause($json);
        //print($queryObj);exit;
        $result = $this->connection->query($sql);
    
        if ($result != null) {
            print_r("NUMBER OF ROWS ".$result->num_rows."<br>");
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $rows[] = $row;
                }
                print("NUMBER OF ROWS ".count($rows));
            }
            return $rows;
        } else {
            echo "<br><strong>ERROR RETRIEVING RECORD: <br>" . $queryObj . "<br>" . $this->connection->error . "<br></strong>";
        }
    }

    //move select and where into the class
    //doquery should return a result
    //save the echos to a status
    //if therer when running the query set hasError to true
    function doQuery($query){
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

function insert($obj){
    $values = get_object_vars($obj);
    
    $columns = array_keys($values);

    $tableName = get_class($obj);

    $db = new MysqlDatabase();
	$db->insert($tableName,$columns,$values);
}

function select($json){
    $db = new MysqlDatabase();
    return $db->doSelect($json);
}

function selectClause(){
	$tableName = "car";
	$selectFields = array();
	return "SELECT * FROM $tableName";
}

function whereClause($conditions){
    $where = "";  // Prepare to build a SQL WHERE clause
    $tmp = array();
    
     foreach($conditions as $c){
         $field = $c->field;
         $op = $c->op;
         $value = $c->value;
 
         if(is_int($value)){
             $tmp []= sprintf("%s %s %d",$field,$op,$value);
         } else if($op == 'LIKE'){
             $tmp [] = sprintf("%s %s '%%%s%%'",$field,$op,$value);
         } else {
             $tmp [] = sprintf("%s %s '%s'",$field,$op,$value);
         }
     }
 
     $where .= " WHERE ".implode(' AND ',$tmp);
 
     return $where;
 }
 



//-----------------NOTES-------------------------------------

//OTHER VERSION OF SELECT CLAUSE METHOD

// function selectClause($field){
// 	$tableName = "car";
// 	$selectFields = array();

// 	$fields = array(
// 	"subject_1","subject_2",
// 	"summary","result",
// 	"title","plaintiff",
// 	"defendant","citation",
// 	"month","day","year",
// 	"circut","majority","judges");

// 	foreach($fields as $f){
// 		if($f == $field){
// 			$selectFields[] = $f;
// 		}
// 	}

// 	if(count($selectFields) == 0){
// 		throw new Exception("No valid fields provided");
// 	}
// 	if(count($selectFields) == 1){
// 		$fieldsList = $selectFields[0];
// 	}
// 	if(count($selectFields) >= 2){
// 		$fieldsList = implode(",",$selectFields);
// 	}

// 	return "SELECT $fieldsList FROM $tableName";
// }

