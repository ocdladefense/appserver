<?php
class DocumentParser extends DomDocument {


    private $filters = array();
    
    
    private $targets = array();
    
    
    private $original;
    
    
    private $filtered;
    
    

    public function __construct($body){
        parent::__construct();
				$encoded = mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8');
        $this->original = $encoded;
        libxml_use_internal_errors(true);
        $this->loadHTML($encoded);
        
        $bodies = $this->getElementsByTagName("body");
        
        if($bodies->length > 0) {
        	$html = $this->saveHTML($bodies[0]);
					$html = "<!doctype html><html><head><meta charset='utf-8' /><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head>".$html."</html>";
        } else {
        	$html = $this->saveHTML();
					$html = "<!doctype html><html><head><meta charset='utf-8' /><meta http-equiv='Content-Type' content='text/html; charset=utf-8' /></head><body>".$html."</body></html>";
        }

				$this->loadHTML($html);
				
        libxml_clear_errors();
    }


		public function fromTarget($selector) {
				$target = $this->getElementById($selector);
				if(null == $target) {
					throw new Exception("ELEM_NOT_FOUND_ERROR: {$target} not found.");
				}
				$html = $this->saveHTML($target);
				
				return new DocumentParser($html);
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

			return $resp->getStatusCode() != 200 ? null : new DocumentParser($resp->getBody());
    }

}