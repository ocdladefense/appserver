<?php

namespace File;
use \Http\IJson as IJson;

class FileList implements IJson {

    private static $MISSING_FILE_ERROR_CODE = 4;

    private $files = array();

    private $status;

    public function __construct() {}

    public static function fromHandler($handler){

        $fList = new FileList();

        $dirs = $handler->getSourcePaths();
        
        $paths = FileList::listDirectoryFilesMultiple($dirs);

        foreach($paths as $path){

            $file = File::fromPath($path);

            $fList->addFile($file);
        }

        return $fList;
    }


    //SETTERS

    public function setStatus($status){

        $this->status = $status;
    }

    //GETTERS
    public function getFiles(){
        return $this->files;
    }

    public function getFile($name){

        foreach($this->files as $file){

            if($file->getName() == $name){
                return $file;
            }
        }
        return null;
    }

    public function getFileAtIndex($index){

        return $this->files[$index];
    }
    
    public function getFirst() {
    	return $this->files[0];
    }

    public function size(){

        return count($this->files);
    }

    public function isEmpty(){

        return count($this->files) == 0;
    }


    //ADDITIONAL MEHTODS
    public function addFile($file){

        $this->files[] = $file;
    }

    public function addFiles($files){

        foreach($files as $file){
            
            $this->files[] = $file;
        }
    }

    public function save() {


    }

    public function validate(){

        foreach($this->files as $file){

            $this->handler->validate($file);
        }
    }

    public function hasMissingFileError(){

		foreach($this->postData as $files){
			foreach($files["error"] as $error){
				if($error == self::$MISSING_FILE_ERROR_CODE){
					return true;
				}
			}
		}

		return false;
    }
    
    //ToDo: We want to recursivly iterate through the directory
    //ToDo: How do we represent folders
    public static function listDirectoryFiles($dir){
		$fileNames = array();

		if ($handle = opendir($dir)) {

			while (false !== ($entry = readdir($handle))) {
		
				if ($entry != "." && $entry != "..") {
		
					$fileNames[] = $dir . "/" . $entry;
				}
			}
		
			closedir($handle);
		}

		return $fileNames;
    }

    public static function listDirectoryFilesMultiple($dirs){

        $fileNames = array();

        foreach($dirs as $dir){

            $fileNames += FileList::listDirectoryFiles($dir);
        }

        return $fileNames;

    }

    //Currently we are assuming that every instance of filelIst needs a handler to set the appId and userId
    public function toJson(){

        $response = array(
            "files"     => $this->files,
            //"appId"     => $this->handler->getAppId(),
            //"userId"    => $this->handler->getUserId(),
            "status"    => $this->status
        );

        return json_encode($response);
    }

    public function createProperty($propName, $propValue){

        $this->{$propName} = $propValue;
    }


}