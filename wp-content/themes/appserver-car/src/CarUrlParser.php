<?php
class CarUrlParser{
    
    // private $URL_TO_PAGE = "https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_November_27,_2019";
    const URL_TO_PAGE = "https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_";

    private $protocol;
    private $domain;
    private $path;
    private $carUrl = array();
    private $url;
    private $stringDate;


    function __construct($date){

        $this->stringDate = $date->format("F_j,_Y");

        list($this->protocol,$this->url) = explode("//",$this->URL_TO_PAGE);

        list($this->domain,$this->path,$this->carUrl) = explode("/",$this->url);


    }

    function getProtocol(){
        return $this->protocol;
    }

    function getDomain(){
        return $this->domain;
    }

    function getPath(){
        return $this->path;
    }

    function toUrl(){

        $url = self::URL_TO_PAGE.$this->stringDate;
        return $url;
    }
}