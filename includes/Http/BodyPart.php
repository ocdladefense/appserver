<?php

namespace Http;

class BodyPart {

    public $headers = array();

    public $content;


    // Conforms to Http spec for multipart form data.
    public function __toString(){

        $content = array(
            //"--{$this->boundary}",
            $header1,
            $header2,
            "",
            $content,
            ""
        );

        return implode("\n", $content);
    }
}