<?php
class DocumentParser extends DomDocument {


    private $filters = array();
    
    
    private $targets = array();
    
    
    private $filtered;
    
    
    private $xpath;
    

    public function __construct($body){
        parent::__construct();

				// $encoded = mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8');

        libxml_use_internal_errors(true);
        
        $this->loadHTML($body);
        
        $bodies = $this->getElementsByTagName("body");
        
        
        print $bodies[0]->textContent;
        
        
        if($bodies->length > 0) {
        	$html = $this->saveHTML($bodies[0]);
					$html = "<!doctype html><html><head><meta charset='utf-8' /><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head>".$html."</html>";
        } else {
        	$html = $this->saveHTML();
					$html = "<!doctype html><html><head><meta charset='utf-8' /><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head><body>".$html."</body></html>";
		}
		
		$this->loadHTML($html);
				
        libxml_clear_errors();
        
		$this->xpath = new DomXPath($this);
    }


		public function fromTarget($selector) {
				$target = $this->getElementById($selector);
				if(null == $target) {
					// throw new Exception("ELEM_NOT_FOUND_ERROR: No nodes found for <em>{$selector}</em>");
				} else {
					$html = $this->saveHTML($target);				
				}

				
				return null == $target ? null : new DocumentParser($html);
		}



		public function getValue($selector) {
			$elems = $this->getElementsByClassName($selector);
		
			return $elems->length > 0 ? $elems->item(0)->nodeValue : null;
		}
		
		
		public function getText($selector) {
			$elems = $this->getElementsByClassName($selector);
		
			return $elems->length > 0 ? $elems->item(0)->textContent : "";
		}
	
	
	
		// https://www.webperformance.com/
			// load-testing-tools/blog/articles/real-browser-manual/
				// building-a-testcase/how-locate-element-the-page/xpath-locator-examples/
		public function getElementsByClassName($name) {
			$selector = "//div[contains(@class,'{$name}')]";
			return $this->xpath->query($selector);
		}



    public function filter($tags = null){
        
        if(gettype($tags) == "string") {
        	$tags = array($tags);
        }
        
        foreach($tags as $tag){
            $elements = $this->getElementsByTagName($tag);
            for($i = 0; $i < count($elements); $i++){
                $elements->item($i)->parentNode->removeChild($elements->item($i));
            }
        }
        
        return $this;
    }

    
    public function text(){
    	return $this->getElementsByTagName("body")[0]->textContent;
    }

    
    public function html(){
    	return $this->saveHTML();
    }

    
    public function extract($target) {
    	return $this->saveHTML($target);
    }
    
    
    public static function fromUrl($url) {
			$req = new HttpRequest($url);
	
			$resp = $req->send();
			$body = $resp->getBody();
			

			return $resp->getStatusCode() != 200 || self::isEmpty($body) ? null : new DocumentParser($body);
    }
    
    private static function isEmpty($str) {
			return "" == trim($str);
	}
	
	public function isDraft(){
		$string = $this->saveHtml();

		return strpos($string,"is a draft") !== false;
	}
}