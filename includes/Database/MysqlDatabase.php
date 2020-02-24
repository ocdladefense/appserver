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
        //Create connection
        $this->connection = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

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
        $sql = "INSERT INTO $tableName ($columnNames) VALUES ('$formatted')";

        $result = $this->doQuery($sql);
        return $result->status;
    }

    function doSelect($json){
        $rows = array();

        $sql = $this->selectClause().$this->whereClause($json);
        //$sql = "SELECT * FROM over there";
        $result = $this->doQuery($sql);
    
        if ($result->data != null) {
            if($result->data->num_rows > 0){
                while($row = $result->data->fetch_assoc()){
                    $rows[] = $row;
                }
            }
            return $rows;
        } else if($result->hasError) {
            return $result->status;
        }
        throw new DbException("Something unexpected happened....not quite sure what it is yet though.");
    }

    function doQuery($query){
        $conn = $this->connection->query($query);
        $result = new StdClass();
        $result->hasError = false;

        if(strpos($query,"INSERT") !== false){
            if ($conn === TRUE) {
                $result->status = "<br><strong>New record created successfully<br></strong>";
            } else {
                $result->hasError = true;
                $result->status = "<br><strong>ERROR CREATING RECORD: <br>" . $query . "<br>" . $conn->error . "<br></strong>";
            }
        }

        if(strpos($query,"SELECT") !== false){
            if($conn != null && $conn->num_rows > 0){
                $result->data = $conn;
            }
            else{
                $result->hasError = true;
                $result->status = "<br><strong>ERROR RETRIEVING RECORD: <br>" . $query . "<br>" . $conn->error . "<br></strong>";
            }
        }

        return $result;
    }
    
    function close(){
        $this->connection->close();
    }

    //Additional Methods

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
}

function insert($obj){
    $values = get_object_vars($obj);
    
    $columns = array_keys($values);

    $tableName = get_class($obj);

    $db = new MysqlDatabase();
	return $db->insert($tableName,$columns,$values);
}

function select($json){
    $db = new MysqlDatabase();
    return $db->doSelect($json);
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

