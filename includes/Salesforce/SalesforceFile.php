<?php

namespace Salesforce;

use File\File;

class SalesforceFile extends File {

    public function getSObject() {

        return get_object_vars($this);
    }

}