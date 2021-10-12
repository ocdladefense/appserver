<?php

class QueryException extends Exception {

    function __construct($message){
        $this->message = $message;
    }
}