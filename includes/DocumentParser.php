<?php
class DocumentParser extends DomDocument {


    private $filters = array();
    
    
    private $targets = array();
    
    
    private $original;
    
    
    private $filtered;
    
    

    public function __construct($body){
        parent::__construct();
        $this->original = $body;
        libxml_use_internal_errors(true);
        $this->loadHTML($body);
        libxml_clear_errors();
    }


		public function fromTarget($selector) {
				$html = $this->saveHTML($this->getElementById($selector));
				
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
        
    }


		/*
    public function text(){
    		$content = "";
    		
    		foreach($this->targets as $target) {
        	$elem = $this->getElementById($target);
        	$content .= $elem->textContent;
        }
        
        
        return $content;
    }
    */
    
    public function text(){
    	return $this->getElementsByTagName("body")[0]->textContent;
    }

}