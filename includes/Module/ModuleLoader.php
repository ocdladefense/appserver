<?php



class ModuleLoader {


		private $index;


    public function __construct($index) {
    	$this->index = $index;
    }



    // Load one or more modules for the specified paths.
    public function loadModules($paths) {

        $previous = getcwd();

        foreach($paths as $mod)  {
            require_once($mod."/module.php");
        }
        
    }
    
    
    
    public function load($name) {
    	if(!isset($this->index[$name])) {
    		throw new Exception("MODULE_NOT_FOUND_ERROR: {$name}.");
    	}
    	$module = $this->index[$name];
    	$path = $module["path"];
        
        if($path == null) return;
        
    	require_once($path."/module.php");
    	
    	foreach($module["files"] as $file) {
    		require($path . "/src/" . $file);
    	}
    }
    
    
    
    public function loadObject($name) {
    	$this->load($name);
    	return self::getInstance($name);
    }
    
    
    
    // Require each of the dependencies for each module
    public static function getInstance($moduleName) {
    		if(empty($moduleName)) {
    			throw new Exception("MODULE_ERROR: Cannot instantiate empty module class.");
    		}
    		
        $className = ucwords($moduleName,"-\t\r\n\f\v");
        $className = str_replace("-","",$className)."Module";
        $moduleClass = new $className();
        $dependencies = $moduleClass->getDependencies();

        foreach($dependencies as $d){
            $instance = self::getInstance($d);
            $instance->loadFiles();
        }
        return $moduleClass;
    }
}