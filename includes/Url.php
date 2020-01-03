<?php
class Url{
    public __construct($path){
        
    
    

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

          $namedParameters = array();
          // class UrlParser($url){
              $firstThing = explode("   ?", $url);
            if($this->hasQueryString($url)){
                $this->queryString = explode("?",$url)[1];
            }

            $this->everyThingNotPartOfQueryString = explode("?",$url)[0];

            //even this has two possible outcomes 4 works with no further processing
            if($this->hasProtocol()){
                $this->protocol = explode("//", $this->everythingElse)[0];
                $this->path = explode("//", $this->everythingElse)[1];
                $this->hostName = explode("/", $this->path)[0];
            }
            else{
                $this->path = explode("//", $this->everythingElse)[0];
            }
            

            



              // global $PATH_TO_APP_DOT_PHP --> need to strip this off is present


              $path = $firstThing[0];
              $this->queryString = $firstThing[1];
            if($queryString != null){
                $this->parseQueryString();
            }
              /// Now further processing:
          }
          // close constructor

          public function getResourceString($basePath = null){
              return $this->resourceString;
          }

          public function hasProtocol($everyThingElse){
              return count(explode("//",$everyThingElse)) > 1;
          }


          public function /* boolean */ hasQueryString($path){
              return count(explode("?",$url)) > 1;
          }

          public function speacialKindOfCheck(){
            if(strpos($this->completeRequestedPath,"/") === 0){
                $this->completeRequestedPath = substr($this->completeRequestedPath,1);
            }
          }

          public function parseArguments(){
            if(strpos($this->completeRequestedPath,"?") == false){
                //isolate the resource string from the completeRequestedPath
                $parts = explode("/", $this->completeRequestedPath);
                $this->resourceString = $parts[0];
                
                //isolate the arguments from the completeRequestedPath
                if(array_key_exists(1,$parts)){
                    for($i = 1; $i < count($parts); $i++){
                        $this->arguments[$i-1] = $parts[$i];
                    }
                }
            }
        }

          

          public function parseQueryString(){



            else{
                $parts = explode("?", $this->completeRequestedPath);
                $this->resourceString = $parts[0];
                $vp = explode("&",$parts[1]);
    
                //isolate the arguments from the completeRequestedPath
                if(array_key_exists(0,$vp)){
                    for($i = 0; $i < count($vp); $i++){
                        $arg = explode("=",$vp[$i]);
                        $this->arguments[$arg[0]] = $arg[1];
                    }
                }
            }
          }
    }
}