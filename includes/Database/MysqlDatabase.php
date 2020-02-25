<?php
class MysqlDatabase{

    private $connection;

    function __construct(){
        $this->connect();
    }

    function connect(){
        //Create connection
        $this->connection = new Mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);

        // Check connection
        if ($this->connection->connect_error) {
            //SHOULD THROW A DBCONNECTION ERROR
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    function insert($sql){

        //builder


        $result = $this->connection->query($sql);
        $iSelect = new DbInsertResult($result);

        return $iSelect;


        //new instance of insertResult 
        //get the id of the row i just inserted
        //read the docs on how to get the ids after insert
    }

    function select($sql){
        $result = $this->connection->query($sql);
        return new DbSelectResult($result);
    }

    public static function query($sql){
        $db = new MysqlDatabase();

        return $db->select($sql);
    }

    function prepareData($data){

        $addSlashedValues = array();
        
        foreach($data as $value){
            $addSlashedValues[] = addslashes($value);
        }

        return $addSlashedValues;
    }
    
    function close(){
        $this->connection->close();
    }
}

function insert($obj){
    //array_map
    //docs for multiple inserts
    //INSERT INTO car(case,summary) VALUES('escaped1','escaped2'),('escaped3','escaped4');

    $values = get_object_vars($obj);
    
    $columns = array_keys($values);

    $tableName = get_class($obj);

    //use the querybuilder to build insert statement
    $builder = new QueryBuilder();
    $bulder->setTable("car");
    $builder->setColumns($columns);
    $builder->prepare($values);
    $sql = $builder->compile();

    $db = new MysqlDatabase();
	return $db->insert($sql);
}



//doQyery
// function doQuery($query){
//     $conn = $this->connection->query($query);
//     $result = new StdClass();
//     $result->hasError = false;

//     if(strpos($query,"INSERT") !== false){
//         if ($conn === TRUE) {
//             $result->status = "<br><strong>New record created successfully<br></strong>";
//         } else {
//             $result->hasError = true;
//             $result->status = "<br><strong>ERROR CREATING RECORD: <br>" . $query . "<br>" . $conn->error . "<br></strong>";
//         }
//     }

//     if(strpos($query,"SELECT") !== false){
//         if($conn != null && $conn->num_rows > 0){
//             $result->data = $conn;
//         }
//         else{
//             $result->hasError = true;
//             $result->status = "<br><strong>ERROR RETRIEVING RECORD: <br>" . $query . "<br>" . $conn->error . "<br></strong>";
//         }
//     }

//     return $result;
// }