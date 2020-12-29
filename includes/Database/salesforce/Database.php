<?php

namespace Salesforce;


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
			
			$force = new Salesforce();
			$records = $force->createQueryFromSession($selectQuery);
			
			// parse the UPDATE to get the props.
			
			foreach($records as $record) {
				$record[$updatefield] = $updateValue;
				
			}
			
			
			$results = $force->updateRecords($records);
			return $results;
	}
	*/
	
	
	function delete($records) {}
	
	function select($query) {
		global $oauth_config;

		//OAuth
		$salesforce = new \Salesforce($oauth_config);
		
		return $salesforce->CreateQueryFromSession($query);
	}
	
	
	// This is just an alias for "select".
	public static function query($query) {

		$db = new Database();
		
		return $db->select($query);
		
	}

	public static function insert2($query){
		global $oauth_config;

		//OAuth
		$salesforce = new \Salesforce($oauth_config);

		//INSERT INTO Media__c (ResourceId__c, Name, Speakers__c, Description__c, IsPublic__c, Published__c, Date__c) VALUES ('YtoqDF8EnsA', 'name', 'speakers', 'description', true, true, '2020-02-02')


		$statement = explode("INSERT INTO", $query);

		//$statement[0] is empty
		//$statement[1] is everything to the right of INSERT INTO

		//Media__c(ResourceId__c,Name, Speakers__c, Description__c, IsPublic__c, Published__c, Date__c)VALUES('YtoqDF8EnsA', 'name', 'speakers', 'description', true, true, '2020-02-02'),(THESE ARE MY OTHER VALUES)
		//if we explode by space... array = {"Media__c", "(ResourceId__c,", "Name,")}

		//            ),  (

		//There can be more than ONE value list!
		//Keep it in mind
		//Strings could contain commas, or parentheses, but DO contain spaces

		

		$protoKeyValues = explode($statement[1], "VALUES");
		
		$valueString = preg_replace($protoKeyValues[1], ")\s*,\s*(","),(");  //standardizing multiple records
		//look up preg_replace on php.net!

		$values = explode(protoKeyValues[1], "),(");
		//Thing I want to use for inital set of values should be taking protoKeyValues at index of [1]





		//$protoKeyValues[0] is Media__c(ResourceId__c,Name, Speakers__c, Description__c, IsPublic__c, Published__c, Date__c)
		//$protoKeyValues[1] is ('YtoqDF8EnsA', 'name', 'speakers', 'description', true, true, '2020-02-02')

		$SObjectKey = explode ($protoKeyValues[0], "(");

		//$SObjectKey[0] is Media__c
		//$SObjectKey[1] is ResourceId__c,Name, Speakers__c, Description__c, IsPublic__c, Published__c, Date__c)

		//$SObjectKey[1] are keys
		//$protoKeyValues[1] are values
		$trimKeys = trim($SObjectKey[1], ")");
		$trimValues = trim($keyValues[1], "()");

		//$trimKeys is  ResourceId__c,Name, Speakers__c, Description__c, IsPublic__c, Published__c, Date__c
		//$trimValues is 'YtoqDF8EnsA', 'name', 'speakers', 'description', true, true, '2020-02-02'


		$keys = explode($trimKeys, ',');
		$values = explode($trimValues, ",");

		//$keys is now {"ResourceId__c", "Name", "Speakers__c", "Description__c", "IsPublic__c", "Published__c", "Date__c"}
		//$values is now {"YtoqDF8EnsA", 'My Home Video 1', "Josh Cathey", "Josh gives a lecture", true, true, "2020-02-02"}
		
		$getRidofQuotes = function ($item) {
			return trim($item, "\"'");
		};

		//array_map ( callable|null $callback , array $array , array ...$arrays )?
		$trimmedValues = array_map($getRidofQuotes, $values);

		$record = array_combine($keys ,$trimmedValues);
		//want '' coming in input, but want to strip them out for output using trim(value, "'") using a foreach

		console.log($record);
		//array_map ( callable|null $callback , array $array , array ...$arrays )


		$record = array(
			"ResourceId__c"=>"'YtoqDF8EnsA'",
			"Name"=>"''",
			"Speakers__c"=>"''",
			"Description__c"=>"''",
			"IsPublic__c"=>true,
			"Published__c"=>true,
			"Date__c"=>"''"
		);



		return $salesforce->CreateRecordFromSession($SObjectName, $record);
	}
	
	function close() {}
	
	
	/**
	 * Issue multiple types of query statements at once.
	 */
	function batch($queries = array()) {}


}