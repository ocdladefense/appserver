<?php
class QueryStringParser {

public static function Validate($sql){
    return true;
}

public static function Parse($sql){   

    $sql = "Select ID,Name, Email from Contact where isActive = true";

    //Assume every select needs select
    //Assume every select statement needs to have a from clause
    //Assume Where clause is optional
    //Assume there is at least one field. If no field present then error.
    //Assume one field after from (NEEDS TO BE ADDED STILL)
    //Each token is separated by at least one comma or space


    $sql = strtolower($sql);
    $parts = explode("from", trim($sql));
    $select = $parts[0];
    $from = $parts[1];

            if($parts->sizeof() == 1){
                throw new QueryException("Your sql statement must have From after the Fields");
            }
            if(strpos($select, "select") !== 0){
                //select doesnt exist
                throw new QueryException("Your sql statement must start with Select");
            }

            $selectSpace = explode("select ", trim($select)); //[0]: "select "   [1]: "     ID,Name, Email"
            if($selectSpace->sizeof() < 2){
                //spaces don't exist
                throw new QueryException("You must have a select followed by a space at the beginning of your statement");
            }
            if(strpos($from, " ") == false){
                //spaces don't exist
                throw new QueryException("Your sql statement must have spaces in it");
            }

            $wordArray = explode(' ', trim($sql));
            if($wordArray[0] != "select"){
                //First word is not select. Throw Exception
                throw new QueryException("Select must be the first part of the sql statement");
            }
            if($wordarray->sizeof() == 1){
                throw new QueryException("Must be Fields after Select. Or Spaces...");
            }
            if($wordArray[1] == "from"){
                throw new QueryException("Must have Fields in between Select and From in the sql statement");
            }
            for($pos = 1 ; $pos < $wordArray->sizeof() ; $pos++){
                //if it is the first field and from is after the field, but the field has a comma after it
                if($pos == 1 && strpos($wordArray[$pos], ",") == true && $wordArray[$pos + 1] == "from"){
                    throw new QueryException("If there is only one field, then don't include a comma");
                }
                //if there is not a comma between fields
                if(strpos($wordArray[$pos], ",") == false && $wordArray[$pos + 1] != "from"){
                    throw new QueryException("Commas must be in between Fields of the sql exception");
                }
            }
        }
}