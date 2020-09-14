<?php



class AbsoluteUrl extends Url {




		
		

    public function __construct($url, $params){
		

		 	$str = $this->url = self::normalize($url);

				
			$uri = $query[0];

			
			$this->protocol = count($url) > 1 ? $url[0] : null;
			
			$script = count($url) > 1 ? $url[1] : $url[0];
			
			$parts = explode("/",$script);

			$this->domain = count($uri) > 1 ? $uri[1] : null;
			/*	
					$this->path = $pathParts[1];
			} else {
					$this->path = $pathParts[0];
			}
			*/
		}




}
