<?php
class Template
{
    
    private static $TEMPLATE_EXTENSION = ".tpl.php";
    
    private $name;
    
    private $path;
    
    public function __construct($name){
    	$this->name = $name;
    }
    
    public function render($context = array()){
    	if(!self::exists($this->name)){
    		throw new \Exception("TEMPLATE_ERROR: Template {$this->pathToTemplate()} could not be found.");
    	}
    	extract($context);
    	ob_start();
    	include self::pathToTemplate($this->name);
    	$content = ob_get_contents();
    	ob_end_clean();
    	return $content;
    }
    
		public static function pathToTemplate($name){
			return get_theme_path() ."/".$name.self::$TEMPLATE_EXTENSION;
		}
    
    public static function exists($name){
			return file_exists(self::pathToTemplate($name));
    }
}
