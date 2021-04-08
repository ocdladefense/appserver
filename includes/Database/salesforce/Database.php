<?php

namespace RestApiRequest;


class Database {

	private $credentials;

	function __construct() {}


	
	function connect($credentials = array()) {
		$this->credentials = $credentials;
	}



	
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
			$builder->setType("insert");
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
	
	function update($records) {}
	
	
	/*
	function update($sql) {
	
			$selectQuery = buildSelectFromUpdate($sql);
			
			$force = new RestApiRequest();
			$records = $force->createQueryFromSession($selectQuery);
			
			// parse the UPDATE to get the props.
			
			foreach($records as $record) {
				$record[$updatefield] = $updateValue;
				
			}
			
			
			$results = $force->updateRecords($records);
			return $results;
	}
	*/
	
	
	function select($query) {
		global $oauth_config;

		//OAuth
		$salesforce = new \RestApiRequest($oauth_config);
		
		return $salesforce->CreateQueryFromSession($query);
	}

	public static function delete($query){
		global $oauth_config;

		var_dump(list($sObjectName, $records) = self::parseDelete($query));

		//1 Get conditions

		//2 Issue Query   specifically the ID using the given sObjectName

		//3 returns list of results

		//4 Pass the IDs from the results to the API call


		//OAuth
		$salesforce = new \RestApiRequest($oauth_config);
	}
	
	
	// This is just an alias for "select".
	public static function query($query) {

		$db = new Database();
		
		return $db->select($query);
		
	}

	//step 2
	public static function insert2($query){
		global $oauth_config;

		//OAuth
		$salesforce = new \RestApiRequest($oauth_config);

		list($sObjectName, $records) = self::parseInsert($query);
		//var_dump($sObjectName);
		//var_dump($records);
		
		
		//use For loop to loop through records
		$batches = $salesforce->prepareBatchInsert($sObjectName, $records);
		var_dump($batches);
		
		$variable2 = $salesforce->sendBatchFromSession($batches);
		var_dump($variable2);
		//return $salesforce->sendBatch();

		return $variable2;
	}

	public static function getConditions($sql){
		
		$stmt = explode("WHERE", $sql);
		
		$conds = Count($stmt) > 1 ? $stmt[1] : null;

		if($conds == null)
			return null;
		
		// ResourceId__c = 'YtoqDF8EnsA' OR ResourceId__c = 'EtoqHACEnsQ'"
		//$conds2 = trim($conds, "\"'");

		// ResourceId__c = 'YtoqDF8EnsA' OR IsPublished__c = true AND ResourceId__c = 'EtoqHACEnsQ'
		
		$numOrs = preg_split("/\s+OR\s+/", $conds); //split by OR first
		$numANDs = preg_split("/\s+AND\s+/", $conds);
		//split each index of $conds3 by AND next
		if(Count($numORs) > 1 && Count($numANDs) > 1){
			//throw exception
			throw new Exception("We cannot process conditons with ANDs and ORs");
		}
		else if(Count($numORs) == 1 && Count($numANDs) == 1){
			//1 condition
		}
		else if(Count($numORs) > 1){
			//all ORs
		}		
		else if(Count($numANDs) > 1){
			//all ANDs
		}
		


		//split each section by = then trim spaces using array_map

		
		$conditions = array();
		$values = array();
		foreach($conds3 as $cond){


			$fieldValue = explode("=", $cond);

			var_dump($fieldValue);

			//trim each index of $fieldValue and turn $field into an array
			$field = trim($fieldValue[0]);
			$preValue = trim($fieldValue[1]);

			//trim single quotes from $preValue and turn it into an array
			$value = trim($preValue, "'");

			//want '' coming in input, but want to strip them out for output using trim(value, "'") using a foreach

			$values[] = $value;
			$conditions[$field]= $values;

		}
		var_dump($conditions);

		


		//return the conditons as an array
		return array(
			"op" => "OR",//optional
			"conditions" => array(
				"ResourceId__c" => "YtoqDF8EnsA",
				"ResourceId__c" => "EtoqHACEnsQ"
			)
		);
		
	}

	public static function parseDelete($sql){
		$conditions = self::getConditions($sql);
		//use a select to select the IDs that match the where clause

		//pass IDs to batch endpoint

		$tmp = explode("WHERE", $sql);

		//[0]:  "DELETE FROM Media__c 
		//[1]: CONDITIONS

		$tmp2 = explode("FROM", $tmp[0]);

		//[1]: Media__c 

		$SObject = trim($tmp2[1]);
		
		var_dump($SObject);

		//array with keys
		return array("sobject"=>$SObject, "conditions"=>$conditions);
	}

	
	//step 1
	public static function parseInsert($query){

		$getRidOfQuotes = function ($item) {
			return trim($item, "\"'");
		};

		$getRidOfParen = function ($item) {
			return trim($item, "()");
		};


		function printAll($array, $label = null) {
			print "<h2>{$label}</h2>";
			if(is_array($array)) {
				print "<pre>". print_r($array,true) . "</pre>";
			} else {
				print $array;
			}
		}
		//INSERT INTO Media__c (ResourceId__c, Name, Speakers__c, Description__c, IsPublic__c, Published__c, Date__c) VALUES ('YtoqDF8EnsA', 'name', 'speakers', 'description', true, true, '2020-02-02'),('YtoqDF8EnsA', 'name', 'speakers', 'description', true, true, '2020-02-02')



		$statement = explode("INSERT INTO", $query)[1];

		//$statement[0] is empty
		//$statement[1] is everything to the right of INSERT INTO

		printAll($statement, "INSERT statement is");


		//Media__c(ResourceId__c,Name, Speakers__c, Description__c, IsPublic__c, Published__c, Date__c)VALUES('YtoqDF8EnsA', 'name', 'speakers', 'description', true, true, '2020-02-02'),(THESE ARE MY OTHER VALUES)
		//if we explode by space... array = {"Media__c", "(ResourceId__c,", "Name,")}

		//            ),  (

		//There can be more than ONE value list!
		//Keep it in mind
		//Strings could contain commas, or parentheses, but DO contain spaces

		

		$protoKeyValues = explode("VALUES", $statement);
		
		printAll($protoKeyValues, "Keys and values.");
		
		$valueString = preg_replace("/\)\s*,\s*\(/", "),(", $protoKeyValues[1]);  //standardizing multiple records
		//look up preg_replace on php.net!
		//IF THERE IS NO PATTERN THEN IT WILL RETURN NULL!!

		printAll(trim($valueString," ()"), "Values are:");



		$values = explode("),(", $valueString);
		//Thing I want to use for inital set of values should be taking protoKeyValues at index of [1]

		printAll($values);
		

		//$protoKeyValues[0] is Media__c(ResourceId__c,Name, Speakers__c, Description__c, IsPublic__c, Published__c, Date__c)
		//$protoKeyValues[1] is ('YtoqDF8EnsA', 'name', 'speakers', 'description', true, true, '2020-02-02')

		$SObjectKey = explode ("(", $protoKeyValues[0]);

		//$SObjectKey[0] is Media__c
		//$SObjectKey[1] is ResourceId__c,Name, Speakers__c, Description__c, IsPublic__c, Published__c, Date__c)

		$sObjectName = trim($SObjectKey[0]);
		printAll($SObjectKey, "SObject Keys string is:");


		//$SObjectKey[1] are keys
		//$values are values
		print("<h2>Setting simple string</h2>");
		$simpleString = $SObjectKey[1];
		printAll($simpleString, "Simple string without trim is: ");

		$trimmedKeys = trim($simpleString, ' ()');
		printAll($trimmedKeys, "Trimmed Keys is: ");







		$MultipleRows = array_map(function($item) { return trim($item," ()"); }, $values);
		
		
		
		//$trimKeys is  ResourceId__c,Name, Speakers__c, Description__c, IsPublic__c, Published__c, Date__c
		//$trimValues is 'YtoqDF8EnsA', 'name', 'speakers', 'description', true, true, '2020-02-02'

  
		$keys = array_map(function($item) {return trim($item); }, explode(",", $trimmedKeys));
		printAll($keys, "Keys will be: ");
		//$values = explode($trimmedValues[], ",");

		//$keys is now {"ResourceId__c", "Name", "Speakers__c", "Description__c", "IsPublic__c", "Published__c", "Date__c"}
		//$values is now {"YtoqDF8EnsA", 'My Home Video 1', "Josh Cathey", "Josh gives a lecture", true, true, "2020-02-02"}
		
		$records = array();

		function convertStringtoBool($str){
			if ($str == "true")
				return true;
			elseif ($str == "false")
				return false;
			throw new Exception("Bool must be either true or false");
		}

		foreach($MultipleRows as $Row){

			$values = array_map(function($item) {$tmp = trim($item, " '"); return in_array($tmp, ["true", "false"]) ? convertStringtoBool($tmp) : $tmp; }, explode(",", $Row));

			printAll($keys, "SObject keys will be:");
			
			
			printAll($values, "SObject values will be:");
			

			//array_map ( callable|null $callback , array $array , array ...$arrays )?
			$trimmedValues = array_map($getRidOfQuotes, $values);


			$record = array_combine($keys, $values);
			//want '' coming in input, but want to strip them out for output using trim(value, "'") using a foreach


			$records[] = $record;
		}

		return array($sObjectName, $records);
	}
	
	
	
	
	
	
	
	function close() {}
	
	
	/**
	 * Issue multiple types of query statements at once.
	 */
	function batch($queries = array()) {}


}