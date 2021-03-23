<?php




namespace Salesforce;


use Http\HttpRequest;
use Http\HttpHeader;
use Http\Http;
use Http\HttpResponse;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use Http\BodyPart;
use File\File;


class RestApiEndpoint {

    private $api;
    private $resourcePrefix;
    private $version;
    private $parameters;
    private $endpoint;

    private $url;

    public function __construct() {
        try{
            $this->api = file_get_contents("/OpenApi.json");
            $this->api = json_decode($api);
            $this->resourcePrefix = $api[0];
        }catch( Exception $e){
            throw new Exception ("cannot decode file");
        }

    }

    //url = https://yourInstance.salesforce.com/services/data/v51.0/sobjects/~
    public function setUrl($url){
        $url = trim($url,"salesforce.com")[1];  
        //[1]services/data/v51.0/sobjects/~

        $url = explode($url,$prefix)[1]; 
        //[0]services/data
        //[1]/v51.0/sobjects/~

        $this -> url = $url;
        $parameters = explode($url, "/");//[0] ""  [1]v51.0
        $this -> version = $parameters[1];
        unset($parameters[0]);
        unset($parameters[1]);

        switch ($parameters[0]){
            case "sobjects"://[0] querry //[1]/%sObject/
                unset($parameters[0]);
                setAsBasicInfo($parameters);
                break;
            case "query"://[0] querry //[1]?q=~~~~~~
                unset($parameters[0]);
                setAsQuery($parameters);
                break;
            case "":
                break;
        }
        
    }

    
    public function setQuery($query){///?q=~~~~~~
        $query = str_contains($query[0],"?q=") == true ? trim($query[0],"?q=") : "";


    //look after the ?
    //open and decode this and foreach the endpoints  to located them
    //use params to build the map
    //php querystrings urldecode
    }

    public function getQuerry(){
        
    }

}