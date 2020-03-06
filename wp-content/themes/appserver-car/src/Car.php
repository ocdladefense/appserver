<?php
class Car{
    const CITATION_INDEX = 0;

    const DATE_INDEX = 1;

    const MAJORITY_INDEX = 2;

    const CIRCUT_AND_JUDGES_INDEX = 3;

    const URL_TO_PAGE = "https://libraryofdefense.ocdla.org/Blog:Case_Reviews/Oregon_Appellate_Court,_";

    private $subjectNode;

    private $linkNode;

    private $firstParagraph;

    private $citationNodeValue;

    private $citationNodeValueParts;

    private $subjects;

    public $subject_1;
    public $subject_2;
    public $summary;
    public $result;
    public $title;
    public $plaintiff;
    public $defendant;
    public $citation;
    public $month;
    public $date;
    public $year;
    public $circut;
    public $majority;
    public $judges;
    public $url;

    public function __construct($subjectNode,$linkNode){
        $this->subjectNode = $subjectNode;
        $this->linkNode = $linkNode;
        $this->firstParagraph = $this->subjectNode->parentNode;
        $this->citationNodeValue = $this->linkNode->nextSibling->nodeValue;
        $this->citationNodeValueParts = $this->toArray($this->citationNodeValue);
    }

    function parse(){
        if($this->subjectNode == null){
            throw new CarParserException("The subject node cannot be null");
        }

        if($this->linkNode == null){
            throw new CarParserException("The link node cannot be null");
        }

        if($this->subjectNode->parentNode->nodeName != "p"){
            throw new CarParserException("parent is not a p element");
        }

        $this->subjects = explode(" - ",$this->subjectNode->nodeValue);
        $this->subject_1 = $this->subjects[0];
        $this->subject_2 = $this->subjects[1];
        $this->summary = $this->setSummary();   

        if($this->summary == null){
            throw new CarParserException("The summary cannot be null");
        }

        $this->result = $this->setCaseResult($this->summary);
        if($this->linkNode == null){
            throw new CarParserException("The link node cannot be null");
        }

        $this->title = $this->linkNode->nodeValue;
        if($this->title == null){
            throw new CarParserException("The title cannot be null");
        }

        list($this->plaintiff,$versus,$this->defendant) = explode(" ",$this->title);

        $this->citation = $this->citationNodeValueParts[self::CITATION_INDEX];

        list($this->month,$this->date,$this->year) = $this->getDecisionDate();

        $this->circut = explode(",",$this->citationNodeValueParts[self::CIRCUT_AND_JUDGES_INDEX])[0];

        $this->majority = substr($this->citationNodeValueParts[self::MAJORITY_INDEX],0,-2);

        $this->judges = $this->getOtherJudges();

        $this->url = self::URL_TO_PAGE.$this->month."_".$this->date.",_".$this->year;
    }
    //362 Or 203 (2017) (Per Curiam)
    

    //---GETTERS---
    function getSubjects(){
        return $this->subjects;
    }

    function getSummary(){
        return $this->summary;
    }

    function getCaseResult(){
        return $this->result;
    }

    function getCaseTitle(){
        return $this->title;
    }

    function getLitigants(){
        return array($this->plaintiff,$this->defendant);
    }

    function getCitation(){
        return $this->citationNodeValueParts[self::CITATION_INDEX];
    }

    function getDecisionDate(){
        //return a usable date array
        $dateArray = substr($this->citationNodeValueParts[self::DATE_INDEX],0,-2);
        $dateArray = preg_split("/[\s,]+/",$dateArray);
        return $dateArray;
    }

    function getCircutCourt(){
        return explode(",",$this->citationNodeValueParts[self::CIRCUT_AND_JUDGES_INDEX])[0];
    }

    function getJudge(){
        return substr($this->citationNodeValueParts[self::MAJORITY_INDEX],0,-2);
    }

    function getOtherJudges(){
        $judges = explode(" ",substr(explode(",",$this->citationNodeValueParts[self::CIRCUT_AND_JUDGES_INDEX])[1],0,-2));
        if($judges[0] == ""){
            array_shift($judges);
            $judges = implode(", ",$judges);
        }
        return $judges;
    }

    //---SETTERS---
    function setSummary(){
        $summaryNodes = array();
        $summary = "";
        $count = 0;
    
        while(++$count < 10){
            $next = $this->firstParagraph->nextSibling;
            $this->firstParagraph = $next;
            if($next->nodeType == XML_TEXT_NODE) continue;
            if($next->firstChild->nodeName == "a") break;
            $summaryNodes[] = $next->nodeValue;
        }
        return implode("\n",$summaryNodes);
    }
    
    function setCaseResult($summaryString){
        $SENTENCE_DELIMITER = ".";
        $sentences = explode($SENTENCE_DELIMITER, $summaryString);
        $result = $sentences[count($sentences)-2];

        return $result;
    }
    function toArray($nodeValue){
        return explode("(",$nodeValue);
    }
}