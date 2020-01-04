<?php
class Url{
    private $protocol;
    private $domain;
    private $basepath;
    private $path;
    private $queryString;
    private $arguments = [];
    private $namedParameters = [];
    private $resourceString;
    private $url;
    private $_url;

    public function __construct($url){
    


    // Format 1: absolute URL
          //  https://appserver/some/path/to/resource/with/1/or/2/additonal-params?and=some&additonanl=params&here=!
        // Formst 2: relatieve URl
          // /some/path/to/resource?other=hello

        // Format 3: relative url
          // some/path/toresource

          // Format 4: instruct user-agent to use current protocol:
          // //appserver/some/path/to/resource
                // Given example resource: path/to/some/resource  = /resource = return 404
          // Format 5: path resolution modifiers
          //  ../go/back/one/directory?hello=world

          // Format 6: be explicity about requesting current directory
           // ./dont/go/back/just/use/this/directory?almost=done

           // Path resolution
            // /path/to/app.php/is/somewhere/else/like/here/(app.php)
            // https://appserver/path/to/app.php/

          // class UrlParser($url){
           $this->_url = $url;
           $url = $this->url = self::normalize($url);
           

            if($this->hasQueryString($url)){
                $this->queryString = explode("?",$url)[1];
                $this->namedParameters = self::parseQueryString($this->queryString);
            }

            $this->everythingElse = explode("?",$url)[0];

            //even this has two possible outcomes 4 works with no further processing
            if($this->hasProtocol($this->everythingElse)){
                $this->protocol = explode("//", $this->everythingElse)[0];
                $this->path = explode("//", $this->everythingElse)[1];
                $this->domain = explode("/", $this->path)[0];
            }
            else{
                $this->path = explode("//", $this->everythingElse)[0];
            }
              /// Now further processing:
              $parts = explode("/", $this->path);
              $this->resourceString = $parts[0];
              $this->arguments=self::parseArguments($parts);
          }
          // close constructor

          public function getResourceString($basePath = null){
              return $this->resourceString;
          }

          public function hasProtocol($everythingElse){
              return count(explode("//",$everythingElse)) > 1;
          }


          public function /* boolean */ hasQueryString($url){
              return count(explode("?",$url)) > 1;
          }

          public static function normalize($path){
            if(strpos($path,"//") === 0 ){
                return $path;
            }
            if(strpos($path,"/") === 0 ){
                $path = substr($path,1);
            }
            return $path;
          }

          public static function parseArguments($parts){
              $arguments = [];
           
                //isolate the resource string from the completeRequestedPath
                //isolate the arguments from the completeRequestedPath
                    for($i = 1, $arg = 0; $i < count($parts); $i++, $arg++){
                        $arguments[$arg] = $parts[$i];
                    }
                    return $arguments;
        }

        public function getArguments(){
            return $this->arguments;
        }

        public function getNamedParameters(){
            return $this->namedParameters;
        }

          

          public static function parseQueryString($queryString){
              $namedParameters = [];
                $kvp = explode("&",$queryString);
                //isolate the arguments from the completeRequestedPath
                    for($i = 0; $i < count($kvp); $i++){
                        $arg = explode("=",$kvp[$i]);
                        
                        $namedParameters[$arg[0]] = $arg[1];
                    }
                    return $namedParameters;
          }
    }
