<?php

namespace Http;

class BodyPart {

    public $headers = array();

    public $content;


    public function setContent($content){

        if($this->getContentType() == 'application/json'){

            $this->content = json_encode($content);
        } else {

            $this->content = $content;
        }
    }


    public function addHeader($hName, $hValue){

        $this->headers[] = new HttpHeader($hName, $hValue);
    }

    public function getContentType(){

        foreach($this->headers as $header){

            if($header->getName() == "Content-Type"){

                return $header->getValue();
            }
        }
    }

    // Conforms to Http spec for multipart form data.
    public function __toString(){

        $hString = implode(";\n", $this->headers);

        $contentArray = array(
            $hString,
            "",
            $this->content,
            ""
        );

        return implode("\n", $contentArray) . "\n";
    }
}