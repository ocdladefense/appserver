<?php
use File\FileHandler as FileHandler;
use File\PhpFileUpload as PhpFileUpload;
use File\File as File;

class CoreModule extends Module {


	private $deps = array();

    public function __construct() {
    
        parent::__construct();
        
        $this->dependencies = $this->deps;
        
        $this->name = "core";
    }


    //Uploads actually happen in HttpRequest
	public function upload(){

		return $this->request->getFiles();
    }
    

	public function download($fileName){

		global $config;
		$handler = new FileHandler($config);
		$path = $handler->getTargetPath() . "/" . $fileName;

		return File::fromPath($path);
	}


    //Rewrite this so that it handles core uploads.  Gonna look in the core upload spot for the files to delete
	public function delete(){
		$postData = $this->request->getBody();
		$fileName = $postData->filename;
		$config = array(
			"appId"		=> $postData->appId,
			"userId" 	=> $postData->userId,
			"path"		=> getUploadPath()
		);

		$handler = new FileHandler($config);

		if (unlink($handler->getTargetPath() . $fileName)) {
			return 'success';
		} else {
			return 'fail';
		}
	}

	public function listFiles(){}
        
}