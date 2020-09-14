<?php



class RelativeUrl extends Url {






    public function __construct($url, $params = array()){
			$this->url = $url;
			
			$this->path = $url;
			/*
		 	$str = $this->url = self::normalize($url);
		 
			$query = explode("?", $str);
				
			$uri = $query[0];

			$this->queryString = count($query) > 1 ? $query[1] : null;
			
			$this->args = self::parseQueryString($this->queryString);

			$url = explode("//", $url);
			
			$this->protocol = count($url) > 1 ? $url[0] : null;
			
			$script = count($url) > 1 ? $url[1] : $url[0];
			
			$parts = explode("/",$script);
			
			if(

			$this->domain = count($uri) > 1 ? $uri[1] : null;
					
					$this->path = $pathParts[1];
			} else {
					$this->path = $pathParts[0];
			}
			*/
		}

}
