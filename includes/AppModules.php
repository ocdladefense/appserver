<?php
class AppModules{
    private static $PATH_TO_MODULES = __DIR__ ."/../modules";
    private $modules = array();

    public function __construct(){}

    //Execute module class functionality and return the "$this->modules" array
    public function getModules(){
        $this->modules = $this->discoverFileSystemModules();
        $this->loadModules($this->modules);
        return $this->modules;
    }

    //Find all of the modules installed in the modules directory and add them to the "$this->modules" array.
    public function discoverFileSystemModules(){
        $previous = getcwd();
        chdir(self::$PATH_TO_MODULES);
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

    //Scan each module directory in the "$this->modules" array and require its "module.php" file.
    public function loadModules($modules){
        $this->modules = $modules;
        $previous = getcwd();
        chdir(self::$PATH_TO_MODULES);

        $files = scandir(".");

        foreach($modules as $mod)  {
            require_once($mod."/module.php");
        }
        chdir($previous);
    }

}