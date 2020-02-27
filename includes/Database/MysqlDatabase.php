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
        
        $result = $this->connection->query($sql);

        $dbir = new DbInsertResult($result,$sql,$this->connection);

        return $dbir->doStuff();


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

    $sample = $objs[0];

    $columns = getObjectFields($sample);

    $values = getObjectValues($objs);

    $tableName = get_class($objs[0]);


    //use the querybuilder to build insert statement
    $builder = new QueryBuilder();
    $builder->setTable($tableName);
    $builder->setColumns($columns);
    $builder->setValues($values);
    $sql = $builder->compile();
    //print($sql);exit;

    $db = new MysqlDatabase();
    //$insertResult = new DbInsertResult($result,$sql,$id,$count);
    $insertResult = $db->insert($sql);
    print($insertResult);exit;
    
    // foreach($objs as $obj){
    //     $obj->id = $insertResult->getNextId();
    // }
}

function getObjectValues($objs){
    return array_map("get_object_vars",$objs);
}
function getObjectFields($obj){
    return array_keys(get_object_vars($obj));
}