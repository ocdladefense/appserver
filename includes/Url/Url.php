<?php



class Url {

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

    public function __construct($url = null){

			if(null == $url) return;

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
			} else {
					$this->path = explode("//", $this->everythingElse)[0];
			}
			
			/// Now further processing:
			$parts = explode("/", $this->path);
			$this->resourceString = $parts[0];
			$this->arguments=self::parseArguments($parts);
		}



		public function getResourceString($basePath = null){
				return $this->resourceString;
		}

		public function hasProtocol($everythingElse){
				return count(explode("//",$everythingElse)) > 1;
		}


		public function hasQueryString($url){
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
