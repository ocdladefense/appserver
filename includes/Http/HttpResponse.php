<?php

namespace Http;


class HttpResponse extends HttpMessage {
	
    private $file;
    
    public function __construct($body = null){

        parent::__construct();

        if($body != null && get_class($body) == "File"){

            $this->file = $body;

            $this->setUpFileDownloadHeaders();
            
        }
        $this->body = $body;
    }

    //FILE DOWNLOAD FUNCTIONALITY
    private function setUpFileDownloadHeaders(){
        
        $fileName = $this->file->getName();
        

        $headers = array(
            new HttpHeader("Cache-Control", "private"),
            new HttpHeader("Content-Description", "File Transfer"),
            new HttpHeader("Content-Disposition", "attachment; filename=$fileName"),
            new HttpHeader("Content-Type", $this->file->getType())
        );

        if($this->isFile()){

            $this->addHeaders($headers);
        }
    }

    public function getFile(){

        return $this->file;
    }

    public function isFile(){

        return get_class($this->file) == "File" && file_exists($this->file->getPath());
    }

    //Setters
    
    public function setContentType($contentType){
            $header = new HttpHeader("Content-Type", $contentType);

            $this->headers->addHeader($header);
    }

    public function setStatusCode($code){
    	$this->statusCode = $code;
    }
    
    public function setErrorStatus(){
        $this->setStatusCode(500);
        $this->statusMessage = "HTTP/1.1 500 Internal Server Error";
        
    }
    
    public function setNotFoundStatus(){
        $this->setStatusCode(404);
    	$this->statusMessage = "HTTP/1.1 404 Page Not Found";
    }
    
    public function setRedirect($url){
        $this->setStatusCode(301);
    	$this->statusMessage = "HTTP/1.1 301 Moved Permanently";
    	$this->headers->addHeader(new HttpHeader("Location",$url));
    }

    public function isSuccess(){
        return $this->getStatusCode() == 200 || $this->getStatusCode() == 201;
    }

    //Getters
    public function getStatusCode(){
        return $this->statusCode;
    }
	
	public function getError(){
		return $this->errorString;
	}

	public function getErrorNum(){
		return $this->errorNum;
	}

	public function success(){
		return $this->status == 200;
	}

    public function getPhpArray(){
        // Parsing the HTTP Response; by parsing we just mean the data has a known format and we can retrieve certain things from the Response.
			return json_decode($this->body, true);
    }

    //other methods

    public function __toString(){
        return $this->body;
    }

}