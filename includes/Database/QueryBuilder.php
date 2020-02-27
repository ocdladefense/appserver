<?php

class QueryBuilder{
    private $tableName;
    private $selectFields;
    private $conditions;

    function __construct(){

    }

    function setTable($tbName){
        $this->tableName = $tbName;
    }

    function setConditions($conds){
        $this->conditions = $conds;
    }
    //Additional Methods

    function selectClause(){
        $this->selectFields = array();
        return "SELECT * FROM $this->tableName";
    }
    
    function whereClause(){
        $where = "";  // Prepare to build a SQL WHERE clause
        $tmp = array();
        
        foreach($this->conditions as $c){
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

    function compile(){
        return $this->selectClause().$this->whereClause();
    }
}