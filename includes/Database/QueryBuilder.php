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

    function selectClause(){
        $this->columns = array();
        return "SELECT * FROM $this->tableName";
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
        } else {
            return $this->selectClause().$this->whereClause().$this->orderByClause().$this->limitClause();
        }
    }

    function getCountQuery() {
        $count = clone($this);
        $count->resetSelect("count(*)");
        $count->resetLimit("");
        return $count;
    }

    function getPageCount($limit) {
        $query = $this->getCountQuery();
        $sql = $query->compile();
		//print($sql);
		return MysqlDatabase::query($sql);
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

        foreach($this->updateFields as $set) {
            $field = $set->field;
            $value = $set->value;
            $op = "=";

            if(is_int($value)){
                $tmp[] = sprintf("%s %s %d",$field,$op,$value);
            } else {
                $tmp[] = sprintf("%s %s '%s'",$field,$op,$value);
            }
        }

        $fields = implode(SQL_FIELD_SEPERATOR, $tmp);
        return $fields;
    }
}