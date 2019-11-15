<?php 
// this class contains methods for working with the file system

class FileSystem {

    public static function getFileList($folderPath, $extension) {
        $folderPath .= "*";
        $folderPath .= $extension;

        // get every json file in the folder path and put it into $files
        $files = glob($folderPath);

        return $files;
    }

    // takes a path to a .json file and converts the file into a php object
    public static function convertJsonFileToObject($file) {
        $handle = fopen ($file, "r");
        $json = fread($handle,filesize($file));
        $object = json_decode($json);

        return $object;
    }

}