<?php


class Identifier {


    public static function format($word, $scheme = "camel-case", $suffix = "") {
        $className = ucwords($word,"-\t\r\n\f\v");

        $replace = "human" == $scheme ? " " : "";
        $className = str_replace("-",$replace,$className).$suffix;

        return $className;
    }

    public static function toMachineName($word) {
        $machine = strtolower($word);

        $machine = str_replace(" ","-",$machine);

        return $machine;
    }
}