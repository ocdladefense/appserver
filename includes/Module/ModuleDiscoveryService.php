<?php



class ModuleDiscoveryService {


    private $modules = array();

		private $start = null;



    public function __construct() {

    }
    
    



    // Find all of the modules installed in the modules
    //   directory and add them to the "$this->modules" array.
    public function fetch($searchPath) {
        $previous = getcwd();
        chdir($searchPath);
        $modules = array();

        $files = scandir(".");

        foreach($files as $dir)  {
            if(!is_dir($dir) || $dir == ".." || $dir == ".")
            continue;
            $modules[] = $dir;
        }
        chdir($previous);
        
        
        return $modules;
    }



}