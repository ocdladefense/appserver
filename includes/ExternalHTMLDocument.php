<?php
class ExternalHTMLDocument extends DomDocument{

    public function __construct($body){
        parent::__construct();
        libxml_use_internal_errors(true);
        $this->loadHTML($body);
        libxml_clear_errors();
    }

    private $tagsToFilterOut = array();
    private $targetIds = array();
    private $filteredHTML;
    private $unfilteredHTML;

    function extractText(){
        $text = $this->getElementById("text");
        return $text->textContent;
    }

    function filter(){

        $filtered = new DomDocument();
        $filtered->loadHTML($this->unfilteredHTML);
        //assume that all images are going to be bad.0
        
        foreach($this->tagsToFilterOut as $tag){
            $elements = $filtered->getElementsByTagName($tag);
            for($i = 0; $i < count($elements); $i++){
                $elements->item($i)->parentNode->removeChild($elements->item($i));
            }
        }
        return $filtered;
    }

    function setTagsToFilter($tags){
        $this->tagsToFilter = $tags;
    }

    function setTargetElementId($id){
        $this->targetIds[] = $id;
    }

    function extractTargets(){
        $text = $this->getElementById("text");

        $innerHTML = "";
        $children = $text->childNodes;
    
        foreach($children as $child) {
            $innerHTML .= $text->ownerDocument->saveHTML($child);
        }
        return $innerHTML;
    }

    function extract(){
        $this->unfilteredHTML = $this->extractTargets();
        $newDoc = $this->filter();
        return $newDoc->saveHTML();
    }

}