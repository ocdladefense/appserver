<?php

define("SQL_FIELD_SEPERATOR",",");
define("SQL_ROW_SEPERATOR",",");
define("SQL_INSERT_ROW_START","(");
define("SQL_INSERT_ROW_END",")");

class QueryBuilder{

    private $tableName;
    private $type;
    private $conditions = array();
    private $sortConditions = array();
    private $limitCondition;
    private $columns = array();
    private $values = array();
    private $updateFields = array();
    private $uikvps = array();

    function __construct(){

    }

    function setTable($tbName){
        $this->tableName = $tbName;
    }

    function setType($tp){
        $this->type = $tp;
    }

    function setConditions($conds){
        $this->conditions = $conds;
    }

    function setSortConditions($conds){
        $this->sortConditions = $conds;
    }

    function setLimitCondition($cond){
        $this->limitCondition = $cond;
    }

    function setColumns($columns){
        $this->columns = $columns;
    }

    function setValues($values){
        $this->values = $values;
    }

    function setUpdateFields($fields){
        $this->updateFields = $fields;
    }

    function setUpdateInsertKeyValuePairs($uikvps) {
        $this->uikvps = $uikvps;
    }

    function selectClause(){
        $this->columns = array();
        return "SELECT * FROM $this->tableName";
    }

    function selectCountClause(){
        return "SELECT count(*) FROM $this->tableName";
    }
    
    function whereClause(){
        $where = "";
        $tmp = array();
        
        foreach($this->conditions as $c){
            if (is_array($c)) {
                $subTmp = array();
                
                foreach($c as $subC) {
                    $subTmp [] = $this->createWhereCondition($subC);
                }

                $tmp [] = "(".implode(' OR ', $subTmp).")";
            } else {  
                $tmp [] = $this->createWhereCondition($c);
            }
        }

        if (count($tmp) > 0) {
            $where .= " WHERE ".implode(' AND ',$tmp);
        }

        return $where;
    }

    function createWhereCondition($c) {
        $field = $c->field;
        $op = $c->op;
        $value = $c->value;

        $returnStr = "";

        if(is_int($value)){
            $returnStr = sprintf("%s %s %d",$field,$op,$value);
        } else if($op == 'LIKE'){
            $returnStr = sprintf("%s %s '%%%s%%'",$field,$op,$value);
        } else if(substr($value, 0, 5) === "(SQL)") {
            $value = str_replace("(SQL)", "", $value);
            $returnStr = sprintf("%s %s %s",$field,$op,$value);
        } else {
            $returnStr = sprintf("%s %s '%s'",$field,$op,$value);
        }

        return $returnStr;
    }

    function orderByClause() {
        $orderBy = "";
        $tmp = array();

        foreach($this->sortConditions as $c){
            $field = $c->field;
            $desc = $c->desc;
            if (gettype($desc) == "string") {
                $desc = filter_var($desc, FILTER_VALIDATE_BOOLEAN);
            }

            if ($desc){
                $tmp [] = $field." DESC";
            } else {
                $tmp [] = $field;
            }
        }

        if (count($tmp) > 0) {
            $orderBy .= " ORDER BY ".implode(", ", $tmp);
        }

        return $orderBy;
    }

    function limitClause() {
        $limit = "";
        if ($this->limitCondition != null && $this->limitCondition != "") {
            $rowCount = $this->limitCondition->rowCount;
            $offset = $this->limitCondition->offset;

            if ($offset != null && $offset > 0) {
                $limit .= " LIMIT " . $offset . ", " . $rowCount;
            } else {
                $limit .= " LIMIT " . $rowCount;
            }
        }
        return $limit;
    }

		function optionsClause(){
			return " LIMIT 100";
		}

    function compile(){
        if($this->type == "insert"){
            $columns = $this->prepareInsertColumns();
            $values = $this->prepareInsertValues();
            return "INSERT INTO $this->tableName $columns VALUES $values";
        } else if($this->type == "update") {
            $fields = $this->prepareUpdateFields();
            return "UPDATE $this->tableName SET $fields".$this->whereClause();
        } else if($this->type == "delete") {
            return "DELETE FROM $this->tableName".$this->whereClause();
        } else if($this->type == "count") {
            return $this->selectCountClause().$this->whereClause();
        } else {
            return $this->selectClause().$this->whereClause().$this->orderByClause().$this->limitClause();
        }
    }

    function getPageCount($limit = null) {
        if ($limit === null) {
            if ($this->limitCondition != null && $this->limitCondition != "") {
                $limit = $this->limitCondition->rowCount;
            } else {
                $limit = 1;
            }
        }

        $clone = clone($this);
        $clone->setType("count");
        $sql = $clone->compile();

        $result = MysqlDatabase::query($sql);
        
        $count;
        //loop is just to access first and only result
        foreach($result as $r) {
            $count = $r["count(*)"];
        }

        return ceil($count / $limit);
    }

    function getCurrentPage() {
        if ($this->limitCondition == null || $this->limitCondition == "") {
            return 0;
        }

        $rowCount = $this->limitCondition->rowCount;
        $offset = $this->limitCondition->offset;

        return ($offset / $rowCount) + 1;
    }
    
    function prepareInsertValues(){
        $sqlr = array();

        foreach($this->values as $row){
            $prepared = array();
            
            foreach($row as $value){
                
                if(is_numeric($value)){
                    $prepared[] = $value;
                } else if(is_string($value)) {
                    $temp = addslashes($value);
                    $prepared[] = sprintf("'%s'",$temp);
                } else if(is_null($value)){
                    $prepared[] = 'NULL';
                }

            }
            $sqlr[] = SQL_INSERT_ROW_START . implode(SQL_FIELD_SEPERATOR,$prepared) . SQL_INSERT_ROW_END ;
        }

        //$this->values = $vArray;
        return implode(SQL_ROW_SEPERATOR,$sqlr);
    }

    function prepareInsertColumns(){
        return SQL_INSERT_ROW_START . implode(SQL_FIELD_SEPERATOR,$this->columns) . SQL_INSERT_ROW_END;
    }

    function prepareUpdateFields(){
        $fields = "";
        $tmp = array();

        if (count($this->values) != 1) {
            return "";
        }

        $updateFields;

        foreach($this->values as $value) {
            $updateFields = $value;
        }

        foreach($this->columns as $field) {
            $value = $updateFields[$field];
            $op = "=";

            if(is_int($value)){
                $tmp[] = sprintf("%s %s %d",$field,$op,$value);
            } else {
                $tmp[] = sprintf("%s %s '%s'",$field,$op,addslashes($value));
            }
        }

        $fields = implode(SQL_FIELD_SEPERATOR, $tmp);
        return $fields;
    }



		static function fromJson($json) {
			return self::fromObject(json_decode($json));
		}



    static function fromObject($obj) {
			// $json = json_decode(urldecode($json));

			if (!is_array($obj)) {
				$obj = [$obj];
			}

			$conditions = array();
			$sortConditions = array();
			$limitCondition = "";
			$columns = [];
			$values = [];
			//$updateFields = array();

			foreach($obj as $cond) {
				if (is_array($cond) || ($cond->type == "condition" && $cond->value != "ALL")) {
					$conditions[] = $cond;
				} else if ($cond->type == "sortCondition") {
					$sortConditions[] = $cond;
				} else if ($cond->type == "limitCondition") {
					$limitCondition = $cond;
				} else if ($cond->type == "insertCondition") {
					if (!in_array($cond->field, $columns)) {
							$columns[] = $cond->field;					
					}
					$values[$cond->rowId][$cond->field] = $cond->value;
				}// else if ($cond->type == "insertCondition" && $type == "update") {
				//	$updateFields[] = $cond;
				//}
			}

			$builder = new QueryBuilder();
			//$builder->setTable($table);
			//$builder->setType($type);
			$builder->setConditions($conditions);
			$builder->setSortConditions($sortConditions);
			$builder->setLimitCondition($limitCondition);
			$builder->setColumns($columns);
			$builder->setValues($values);
			//$builder->setUpdateFields($updateFields);

			return $builder;
    }
}