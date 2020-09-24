<?php

class SalesforceAuthException extends Exception
{
    public function __construct($errorBody){
        $this->message = "Salesforce Auth Exception";
        if(is_array($errorBody) && !is_null($errorBody)){
            $this->message = $this->message.":";
            foreach ($errorBody as $key => $value) {
                if(!is_null($key) || !empty($key)){
                    $this->message = $this->message." \"".$key;
                }
                if(!is_null($value) || !empty($value)){
                    $this->message = $this->message."=".$value."\";";
                }
            }
        }else{
            $this->message = $this->message.": ".$errorBody;
        }
    }
}