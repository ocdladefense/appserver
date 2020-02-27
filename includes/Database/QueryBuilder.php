<?php

class QueryBuilder{
    private $tableName;
    private $selectFields;
    private $selectConditions = array();
    private $insertColumns = array();
    private $insertValues = array();

    function __construct(){

    }

    function setTable($tbName){
        $this->tableName = $tbName;
    }

    function setConditions($conds){
        $this->selectConditions = $conds;
    }

    function setColumns($columns){
        $this->insertColumns = $columns;
    }

    function setValues($values){
        $this->insertValues = $values;
    }
    //Additional Methods

    function selectClause(){
        $this->selectFields = array();
        return "SELECT * FROM $this->tableName";
    }
    
    function whereClause(){
        $where = "";  // Prepare to build a SQL WHERE clause
        $tmp = array();
        
        foreach($this->selectConditions as $c){
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
        //if statement to determine the type of query
        return $this->selectClause().$this->whereClause();
    }

    function buildInsert(){
        //$escapedValues = $this->prepareInsertValues($values);
        $formattedValues = implode("','",$escapedValues);
        $this->insertColumns = implode(", ",$this->insertColumns);
        $sql = "INSERT INTO $tableName ($insertColumns) VALUES ('$formattedValues')";
    }
    
    function prepareInsertValues(){
        $vArray = array();

        foreach($this->insertValues as $values){
            $addSlashedValues = array();
            
            foreach($values as $value){
                $addSlashedValues[] = addslashes($value);
            }
            $vArray[] = "('" . implode("','",$addSlashedValues) . "')" ;
        }

        $this->insertValues = $vArray;

        var_dump($this->insertValues);exit;
    }

    function prepareInsertColumns(){

        // foreach($columns as $c){
        //     $cString = implode(",", array_map(function($string) {
        //         return '(' . $string . ')';
        //     }, $c));

        //     $cArray[] = $cString;
        // }
        foreach($this->insertColumns as $c){
            $cString = "(" . implode(",",$c) . ")";

            $cArray[] = $cString;
        }

        $this->insertColumns = $cArray;
        //var_dump($this->insertColumns);exit;
    }
}