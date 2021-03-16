<?php


namespace Http;

use \stdClass as stdClass;
use File\FileHandler as FileHandler;
use File\PhpFileUpload as PhpFileUpload;
use File\FileList as FileList;
use Http\BodyPart as BodyPart;


class HttpRequest extends HttpMessage {


	protected $method = "GET";
	
	protected $parts = array();

	protected $host;
		
	
	protected $path;

	
	private $port;
	
	
	protected $params = array();
	
	
	private $files = null;

	private $platform = "";

	
	const ALLOWED_VERBS = array(
		"GET",
		"OPTIONS",
		"POST",
		"PUT",
		"PATCH",
		"DELETE"
	);


	public function setPlatform($env){

		$this->platform = $platform;
	}

	public function addPart(BodyPart $part){

		$this->parts[] = $part;
	}


	public function resetParts(){

		$this->parts = array();
	}


	public function setFiles($files) {
		$this->files = $files;
	}

	public function getRequestUri() {
		return $this->getHeader("Request-URI")->getValue();
	}
	
	public function headerLike($name, $value) {
		return $this->getHeader($name) == null ? false : $this->getHeader($name)->equals($value);
	}
	
	public function isJson() {
		return $this->headerLike("Content-Type", "application/json");
	}
	
	public function isForm() {
		return $this->headerLike("Content-Type", MIME_FORM_URLENCODED);	
	}
	
	public function isMultipart() {

		$multipart = $this->headerLike("Content-Type", MIME_MULTIPART_FORM_DATA);

		$header = $this->headers->getHeader("Content-Type", false);

		$probably = $header->equals(MIME_MULTIPART_FORM_DATA);

		if(!$multipart && $probably){

			throw new \Exception("CONTENT_TYPE_ERROR: There is probably a typo in your 'Content-Type'.");
		}

		return $multipart;
	}

	public function setContentType($value){

		$this->addHeader(new HttpHeader("Content-Type", $value));
	}


	public function __construct($url = null) {
		parent::__construct();
		if(null != $url) {
			$this->setUrl($url);
		}
	}
	
	
	public function setUrl($url) {
	
		$this->url = $url;

		list($this->host, $this->path) = self::parseHostname($this->url);


		$this->headers->addHeader(new HttpHeader("Host",$this->host));
	}
	
	
	public function setMethod($method) {
		$this->method = $method;
	}
	
	public function getMethod() {
		return $this->method;
	}


	public function getUrl() {
		return $this->url;
	}

	public function getArguments(){
		$url = new \Url($this->url);

		return $url->getArguments();
	}

	public function getUrlNamedParameters(){
		$url = new \Url($this->url);

		return $url->getNamedParameters();
	}


	private static function parseHostname($url) {
		list($scheme,$address) = explode("://",$url);

		$parts = explode("/",$address);

		$host = array_shift($parts);
		return array($host, "/".implode("/",$parts));
	}


	public function getHost() {
		return $this->host;
	}

	public function setPost(){
		$this->method = HTTP_METHOD_POST;
	}

	public function setPut(){
		$this->method = HTTP_METHOD_PUT;
	}
	
	public function isGet() {
		return $this->method == HTTP_METHOD_GET;
	}
	
	public function isPost(){
		return $this->method == HTTP_METHOD_POST;
	}
	

	
	
	public function setPatch(){
		$this->method = HTTP_METHOD_PATCH;
	}
	
	public function setDelete(){
		$this->method =  HTTP_METHOD_DELETE;
	}
	
	// @deprecated
	// Instead use getRequestMethod()
	public function getRequestType() {
		return $this->method;
	}
	
	public function getRequestMethod() {
		return $this->method;
	}
	
	
	public function setPort($port) {
		$this->port = $port;
	}
	
	

	public function getBody() {


		return $this->isMultipart() && $this->platform != "apache" ? $this->getMultiPartBody() : $this->body;
		
	}


	public function getMultiPartBody(){

		$body = "";
		$contentTypeHeader = $this->getHeader("Content-Type");
		$contentTypeHeaderParams = $contentTypeHeader->getParameters();
		
		if($contentTypeHeaderParams["boundary"] == null){

			throw new \Exception("No boundary parameter in Content-type header.");
		}

		$boundary = $contentTypeHeaderParams["boundary"];

		// parse the contenttype Header to get the actual boundary.

		foreach($this->parts as $part){

			$body .= "--{$boundary}\n";
			$body .= $part->__toString();
		}

		$body .= "--{$boundary}--";

		return $body;
	}
	
	/**
	 * @todo needs to return an MessageBody object,
	 *   that has ->text(), ->json(), ->value()->, and ->files() methods.
	 */
	public function getFiles() {
		return $this->files;
	}


	
	
	
	public function isSupportedContentType($contentType){

		return true;

		
		$accept = $this->getHeader("Accept")->getValue();

		return $accept == $contentType || stringContains($accept, "*/*");
	}
	




	
	
	
	public function getPath(){

	}

	public function setParams($p){
		if(is_array($p)) {
			  $_params = array();
			  foreach($p as $key=>$value){
				  $_params[] = $key ."=".$value;
			  }		
			  $this->params = implode('&',$_params);
		}
		else {
			$this->params = $p;
		}
  
	}


	public static function newFromApacheEnvironment() {
		$env = new stdClass();
		$server = new stdClass();
		
		
		$http = new stdClass();
		$http->headers = apache_request_headers();
		
		$server = array(
			"requestUri" => $_SERVER["REQUEST_URI"],
			"requestMethod" => $_SERVER["REQUEST_METHOD"]
		);
		
		$env->server = $server;
		$env->http = $http;
		
		return $env;
	}


	/**
	 * @newFromEnvironment
	 *
	 * @description
	 *   We can reconstruct the environment from the $envkey.
	 *  Currently only supports $envkey="apache".
	 */
	public static function newFromEnvironment($envkey = "apache") {
		
		$env = self::newFromApacheEnvironment();

		
		$request = new self($env->server["requestUri"]);
		$request->setPlatform($envkey);
		$request->setMethod($env->server["requestMethod"]);
		
		// @todo see if this can't be moved into the constructor.
		$request->addHeader(new HttpHeader("Request-URI", $env->server["requestUri"]));

		if($request->isPost()) {
			$request->addHeader(new HttpHeader("Content-Type", $env->http->headers["Content-Type"]));
		}


		// GET requests cannot have a body.
		// Otherwise determine the data structure that best represents the message body.
		// Prevents us from having to continually call json_decode, etc,
		// so $request will just get the data structure.
		// @todo - May need a new MessageBody class.
		if($request->isGet()) {
			$request->setBody(null);
			
		} else if($request->isPost() && $request->isForm()) {
			$request->setBody((object)$_POST);
			
		} else if($request->isPost() && $request->isMultipart()) {

			$request->setBody((object)$_POST);


			//if first index is empty dont execute?
			if(is_array($_FILES) && !empty($_FILES) ){
					//try moving and uploading files
				try {
					global $fileConfig;
				
					$handler = new FileHandler($fileConfig);
		
					$handler->createDirectory();
		
					$uploads = new PhpFileUpload($_FILES);
					$tempList = $uploads->getTempFiles();
					$destList = $uploads->getDestinationFiles();
		
					$dFiles = $destList->getFiles();
					$movedFiles = new FileList();
	
					$i = 0;
					foreach($tempList->getFiles() as $tFile){
			
						$dest = $handler->getTargetFile($dFiles[$i]);
			
						$handler->move($tFile, $dest);
	
						$movedFiles->addFile($dest);
		
						$i++;
					}
	

					$request->setFiles($movedFiles);

				} catch(\Exception $e){

					throw $e;

				}
			}


	
			
		} else if(!$request->isGet()) {
			$content = file_get_contents('php://input');
			
			$body = $request->isJson() ? json_decode($content) : $content;
			
			$request->setBody($body);
			
		} else {
			$request->setBody(null);
		}
			
		return $request;
	}



	public function addParameter($name, $value){
		$this->params[] .= $name ."=". $value;
	}
	
	
}