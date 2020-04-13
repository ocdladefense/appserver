<?php
class HttpHeader{
    private $name;
    private $value;

    public function __construct($name,$value){
        $this->name = $name;
        $this->value = $value;
    }
}