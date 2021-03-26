<?php

class Session {

    public static function set($namespace, $name, $value){

        if(!isset($_SESSION[$namespace])){

            $_SESSION[$namespace] = array();
        }

        $_SESSION[$namespace][$name] = $value;
    }

    public static function get($namespace, $name){

        return $_SESSION[$namespace][$name];
        
    }
}