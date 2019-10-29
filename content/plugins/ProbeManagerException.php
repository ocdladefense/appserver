<?php
// this class is used for creating a new custom exception when validation in ProbeManager fails
class ProbeManagerException extends Exception {

    public function errorMessage() {
        //error message
        $errorMsg = 'Error on line '.$this->getLine().' in '.$this->getFile()
        .': <b>'.$this->getMessage().'</b> is not a valid E-Mail address';
        return $errorMsg;
      }

    function __construct($file) {
        echo $file;
    }

    // function getMessage() {
    //     //override this method
    // }
}