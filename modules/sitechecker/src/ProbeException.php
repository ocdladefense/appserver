<?php
// this class is used to create a new customized exception when validation in Probe fails
class ProbeException {
    
    function __construct() {

    }

    function getMessage() {
        //override this method
    }
}