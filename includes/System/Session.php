<?php

class Session {

    public static function set($namespace, $flow, $name, $value){

        if(!isset($_SESSION[$namespace])){

            $_SESSION[$namespace] = array();
        }

        if(!isset($_SESSION[$namespace][$flow])){

            $_SESSION[$namespace][$flow] = array();
        }

        $_SESSION[$namespace][$flow][$name] = $value;
    }

    public static function get($namespace,$flow, $name){

        return $_SESSION[$namespace][$flow][$name];
        
    }
}