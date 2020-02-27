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
    
    function close(){
        $this->connection->close();
    }
}

function insert($objs = array()){

    //array_map
    //docs for multiple inserts
    //INSERT INTO car(case,summary) VALUES('escaped1','escaped2'),('escaped3','escaped4');

    $values = parseValues($objs);

    $columns = parseColumns($values);

    $tableName = get_class($objs[0]);


    //use the querybuilder to build insert statement
    $builder = new QueryBuilder();
    $builder->setTable($tableName);
    $builder->setColumns($columns);
    $builder->setValues($values);
    $builder->prepareInsertColumns();
    $builder->prepareInsertValues();exit;
    $sql = $builder->compile();

    $db = new MysqlDatabase();
	return $db->insert($sql);
}

function parseValues($objs){

    $values = array();
    foreach($objs as $obj){
        $values[] = get_object_vars($obj);
    }
    return $values;
}
function parseColumns($values){

    $columns = array();
    foreach($values as $val){
        $columns[] = array_keys($val);
    }
    return $columns;
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