<?php

namespace Ocdla; 

class Session {


    private $config = array();


    public function __construct($config = array()) {
        $this->config = $config;
    }


    public static function set($keys, $value) {
        $key = is_array($keys) ? implode("", $keys) : $keys;
        $_SESSION[$key] = $value;
    }



    public static function get($keys) {
        $key = is_array($keys) ? implode("", $keys) : $keys;
        return $_SESSION[$key];
    }



    // May want to type hint this later
    public static function setUser($user) {

        $_SESSION["user"] = $user;
    }



    public static function getUser() {

        return $_SESSION["user"];
    }
}