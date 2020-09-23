<?php
use File\FileHandler as FileHandler;
use File\PhpFileUpload as PhpFileUpload;
use File\File as File;
use File\FileLIst as FileList;

class CoreModule extends Module {


	private $deps = array();

    public function __construct() {
    
        parent::__construct();
        
        $this->dependencies = $this->deps;
        
        $this->name = "core";
    }


    //List all uploaded files.  Uploads have already happened in HttpRequest.
	public function upload(){

		return $this->request->getFiles();
	}

	public function download($filename){

		global $fileConfig;

		$handler = new FileHandler($fileConfig);

		$file = File::fromPath($handler->getTargetPath(). "/" . $filename);
				
		return $file;
	}

	public function list(){

		global $fileConfig;

		$handler = new FileHandler($fileConfig);

		$fList = FileList::fromHandler($handler);

		return $fList;
	}




    // //Rewrite this so that it handles core uploads.  Gonna look in the core upload spot for the files to delete
	// public function delete(){
	// 	$postData = $this->request->getBody();
	// 	$fileName = $postData->filename;
	// 	$config = array(
	// 		"appId"		=> $postData->appId,
	// 		"userId" 	=> $postData->userId,
	// 		"path"		=> getUploadPath()
	// 	);

	// 	$handler = new FileHandler($config);

	// 	if (unlink($handler->getTargetPath() . $fileName)) {
	// 		return 'success';
	// 	} else {
	// 		return 'fail';
	// 	}
	// }
        
}

	// //Return a json object representing files related to the given appId and userId.
	// public function listFilesRoute(){

	// 	$postData = $this->request->getBody();

	// 	return $this->listFiles($postData->appId, $postData->userId);
	// }

	// public function listFiles($appId, $userId){

	// 	$config = array(
	// 		"path" 		=> getUploadPath(),
	// 		"userId"    => $userId,
	// 		"appId"	    => $appId
	// 	);


	// 	$handler = new FileHandler($config);

	// 	$fList = FileList::fromHandler($handler);

	// 	return $fList;
	// }

	// public function listFilesRouteExample(){

	// 	$appId = "app123";
	// 	$userId = "user123";
	// 	return $this->listFiles($appId, $userId);
	// }

	// public function alteredDownloadExample($fileName, $filePath, $handler, $appId, $userId, $config){

	// 	$postData = $this->request->getBody();
	// 	$fileName = $postData->filename;

	// 	global $config;

	// 	$handler = new FileHandler($config);

	// 	$file = File::fromPath($handler->getTargetPath(). "/" . "Invoice.pdf");
	// 	$file->setType(mime_content_type($file->getPath()));
	// 	var_dump($file);exit;
				
	// 	return $file;
	// }

	// public function alteredDeleteExample(){
	// 	$postData = $this->request->getBody();
	// 	$fileName = $postData->filename;
	// 	$config = array(
	// 		"appId"		=> $postData->appId,
	// 		"userId" 	=> $postData->userId,
	// 		"path"		=> getUploadPath()
	// 	);

	// 	$handler = new FileHandler($config);

	// 	if (unlink($handler->getTargetPath() . $fileName)) {
	// 		return 'success';
	// 	} else {
	// 		return 'fail';
	// 	}
	// }