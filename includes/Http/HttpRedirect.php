<?php

class HttpRedirect extends HttpResponse
{


    public function __construct($url){
    	parent::__construct($body);
    	$this->setRedirect($url);
    }


    public function __toString(){
        return $this->body;
    }

}