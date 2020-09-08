<?php

namespace File;

class PhpFileUpload{

    private $files;



    public function __construct($files){

        $this->files = $files;

    }

    
    public function getTempFiles(){

        $tempList = array();

        foreach($this->files as $upload){

            for($i = 0; $i <= count($upload["name"]) -1; $i++) {

                $params = array(
                    "path"  => $upload["tmp_name"][$i],
                    "type"  => $upload["type"][$i],
                    "size"  => $upload["size"][$i],
                    "error" => $upload["error"][$i]
                );

                $temp = File::fromParams($params);
    
                $tempList[] = $temp;
            }
        }

        $fList = new FileList();
        $fList->addFiles($tempList);

        return $fList;
    }

    public function getDestinationFiles(){

        $fList = new FileList();

        foreach($this->files as $upload){

            for($i = 0; $i <= count($upload["name"]) -1; $i++) {
                
                $params = array(
                    "name"  => $upload["name"][$i],
                    "type"  => $upload["type"][$i],
                    "size"  => $upload["size"][$i],
                    "error" => $upload["error"][$i]
                );

                $destination = File::fromParams($params);
    
                $fList->addFile($destination);
            }
        }
        return $fList;
    }
}