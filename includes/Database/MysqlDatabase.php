<?php


class MysqlDatabase {

    private $connection;

    function __construct(){
        $this->connect();
    }

    function connect(){
        //Create connection
        $this->connection = new Mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);

        // Check connection
        if ($this->connection->connect_error) {
            //SHOULD THROW A DBCONNECTION ERROR
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    function insert($sql){

        $result = $this->connection->query($sql);

        if($result !== true){
        
            throw new DbException("Error inserting data.  " . $this->connection->error);
        }

        $count = mysqli_affected_rows($this->connection);
        if($count == 0){
            throw new DbException("There were ". $count . " rows inserted.");
        }
        $id = mysqli_insert_id($this->connection);
        if($id === null || $id == 0 || $id == ""){
            throw new DbException("The given id cannot be null or equal to 0 or an empty string");
        }


        //print($error);
        return new DbInsertResult($result,$id,$count,$this->connection->error);
    }

    function update($sql){

        $result = $this->connection->query($sql);

        if($result !== true){
            throw new DbException("Error updating data.  " . $this->connection->error);
        }

        $count = mysqli_affected_rows($this->connection);
        if($count == 0){
            throw new DbException("There were ". $count . " rows updated.");
        }

        return new DbUpdateResult($result,$count,$this->connection->error);
    }
    
    function delete($sql){
        $result = $this->connection->query($sql);

        if($result !== true){
            throw new DbException("Error deleting data.  " . $this->connection->error);
        }

        $count = mysqli_affected_rows($this->connection);
        if($count == 0){
            throw new DbException("There were ". $count . " rows deleted.");
        }

        return new DbDeleteResult($result,$count,$this->connection->error);
    }

    function select($sql){
        $result = $this->connection->query($sql);
        return new DbSelectResult($result);
    }

    public static function query($sql, $type = "select"){
        $db = new MysqlDatabase();

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



function select($query) {
	$tokens = explode(" ", strtolower($query));
	$tokens = implode(" ", array_filter($query));
	
	$parts = preg_split("/\s(select|from|where)\s+/",$tokens);
	
	print_r($parts);
	
	
	$fields = $parts[0];
	$object = $parts[1];
	$conditions = $parts[2];
	
	$results = MysqlDatabase::query($query);

	
	return new ListObject($object,$results);
}





class SObjectList {
	
	private $results = array();
	
	private $name;
	
	public function __construct($object,$results = array()) {
		foreach($results as $row) {
				$this->results[] = $row;
		}
	}
	
	public function get($index) {
		return $this->results[$index];
	}
	
	
}





class SObject implements \Http\IJson { // implements \Http\IJson {

	protected $id = null;
	
	protected $type;
	
	protected $result = null;
	
	
	protected function __construct($type,$id) {
		$this->type = $type;
		$this->id = $id;
		$this->result = $this->load($id);
	}
	
	private function load($id) {
		$results = MysqlDatabase::query("SELECT * FROM {$this->type} WHERE id = {$this->id}");

		foreach($results as $row) {
				return $row;
		}
		
	}
	
	public function toJson() {
		return json_encode($this->result);
	}
}





//Global insert function that calls the insert method of the MysqlDatabase class.
function insert($objs = array()){
    $objs = !is_array($objs) ? [$objs] : $objs;
    $invalid = array_filter($objs, function($obj){return $obj->id !== null;});

    if(count($invalid) > 0){
        throw new DbException("Object Id must be null");
    }

    $sample = $objs[0];

    $columns = getObjectFields($sample);

    $values = getObjectValues($objs);

    $tableName = strtolower(get_class($objs[0]));

    //use the querybuilder to build insert statement
    $builder = new QueryBuilder();
    $builder->setTable($tableName);
    $builder->setColumns($columns);
    $builder->setValues($values);
    $sql = $builder->compile();

    $db = new MysqlDatabase();
    $insertResult = $db->insert($sql);
    $counter = 0;

    //give each insertResult an id to save the status of the insert for each object and save it in the application state. 
    foreach($insertResult as $autoId){
        $objs[$counter++]->id = $autoId;

    }
    
    
    

    
}

function getObjectFields($obj){

    if($obj === null){
        throw new DbException("Given object cannot be null");
    }

    $fields = get_object_vars($obj);

    return array_keys($fields);
}

function getObjectValues($objs){

    $values = array_map("get_object_vars",$objs);

    return $values;
}
