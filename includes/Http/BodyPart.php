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

    public static function fromFile($file, $index){
        
        $fileContent = file_get_contents($file->getPath());
        
        $part = new BodyPart();
        $part->addHeader("Content-Disposition","form-data; name=\"binaryPart{$index}\"; filename=\"{$file->getName()}\"");
        $part->addHeader("Content-Type", $part->getMimeType($file->getExt()));
        $part->setContent($fileContent);

        return $part;
    }
    
    public function getMimeType($fileExtension){

		switch($fileExtension){

			case "txt":
				return "plain/text";
				break;
			case "png" || "jpg" || "jpeg" || "jpg" || "gif":
				return "image/{$fileExtension}";
				break;
			case "pdf":
				return "application/pdf";
				break;
            case "doc":
                return "application/msword";
                break;
            case "docx":
                return "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
                break;
            case "mp3":
                return "audio/mpeg";
                break;
            case "mpeg":
                return "video/mpeg";
                break;
            default:
                throw new Exception("FILE_TYPE_ERROR:   File type/extension is not supported.");
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