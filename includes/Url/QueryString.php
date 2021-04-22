<?php



class QueryString {




    public function __construct($query){
		
			
			$this->args = self::parseQueryString($query);

		
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
		
		public static function parseArguments($parts){
			$arguments = [];

			//isolate the resource string from the completeRequestedPath
			//isolate the arguments from the completeRequestedPath
			for($i = 1, $arg = 0; $i < count($parts); $i++, $arg++){
				$arguments[$arg] = $parts[$i];
			}
			return $arguments;
		}


}
