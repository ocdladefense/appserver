<?php

class QueryBuilder{
    private $tableName;
    private $conditions = array();
    private $columns = array();
    private $values = array();

    function __construct(){

    }

    function setTable($tbName){
        $this->tableName = $tbName;
    }

    function setConditions($conds){
        $this->conditions = $conds;
    }

    function setColumns($columns){
        $this->columns = $columns;
    }

    function setValues($values){
        $this->values = $values;
    }

    function selectClause(){
        $this->columns = array();
        return "SELECT * FROM $this->tableName";
    }
    
    function whereClause(){
        $where = "";
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

        if($this->getType() == "insert"){
            $columns = $this->prepareInsertColumns();
            $values = $this->prepareInsertValues();
            return "INSERT INTO $this->tableName $columns VALUES $values";
        } else {
            return $this->selectClause().$this->whereClause();
        }
    }
    
    function prepareInsertValues(){
        $vArray = array();

        foreach($this->values as $vals){
            $addSlashedValues = array();
            
            foreach($vals as $value){
                $addSlashedValues[] = addslashes($value);
            }
            $vArray[] = "('" . implode("','",$addSlashedValues) . "')" ;
        }

        //$this->values = $vArray;
        return implode(",",$vArray);
    }

    function prepareInsertColumns(){
        return "(" . implode(",",$this->columns) . ")";
    }

    function getType(){

        if(debug_backtrace()[2]["function"] == "select"){
            return "select";
        }
        else if(debug_backtrace()[2]["function"] == "insert"){
            return "insert";
        }
        else if(debug_backtrace()[2]["function"] == "update"){
            return "update";
        }
        else {
            return "delete";
        }
    }
}