<?php


class Template {
  
  const DEBUG = false;
  
  
  // Default file extension for template files.
	const TEMPLATE_EXTENSION = ".tpl.php";
	
	
	// Name of this template; usually the name of a template file.
	// Template files may be suffixed with the .tpl.php extension.
	protected $name;

	protected $modulePath;
	
	
	// Whether the template has been rendered / compiled.
	protected $rendered = false;
	
	
	// When rendered, the resulting output should be saved here.
	protected $output = null;
	
	
	// Context to be bound to this template.
	protected $context = array();
	
	
	// Path or paths associated with this template.
  	protected $paths = array();
	

	// Messages to record status of this template.
	protected $log = array();
	
	
	protected static $allScripts = array();
	
	
	protected static $allStyles = array();
	
	
	protected $scripts = array();
	
	
	protected $styles = array();
	
	
	// Name is optional so we can use string literals to contruct a template.
	public function __construct($name = null) {
		$this->name = $name;
	}

	public function getName(){

		return $this->name;
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
		

	public function setModulePath($path){

		$this->modulePath = $path;
	}


	public function addPath($path) {
		$this->paths []= $path;
	}
	
	public function getPaths() {
		return $this->paths;
	}
	

	public function addStyles($styles, $modulePath = null) {

		foreach($styles as $style){

			$style["href"] = $modulePath != null ? $modulePath . "/" . $style["href"] : $style["href"];

			$this->styles[] = $style;
		}
	}

	public function addScripts($scripts, $modulePath = null) {

		foreach($scripts as $script){

			$script["src"] = $modulePath != null ? $modulePath . "/" . $script["src"] : $script["src"];

			$this->scripts[] = $script;
		}
	}
	
	public function getStyles() {
		return $this->styles;
	}
	
	
	public function getScripts() {
		return $this->scripts;
	}
	
	public function getOutput() {
		return $this->output;
	}

	public function getModulePath(){
		return $this->modulePath;
	}
	
	public static function isTemplate($obj) {

		return is_object($obj) && (get_parent_class($obj) == "Template" || get_class($obj) == "Template");
	}


	public function render($context = array()) {
		$this->rendered = true;
		$context = count($context) === 0 ? $this->context : $context;
		
		$this->output = null == $this->content ? $this->renderTemplateFile($context) : $this->renderTemplateString($context);
		return $this->output;
	}
	
	
	public function isRendered() {
		return $this->rendered;
	}
	
	
	public function renderTemplateString($context = array()) {
		extract($context);
		ob_start();
		
		eval('?>'.$this->content);
		
		$content = ob_get_contents();
		ob_end_clean();
		
		
		return $content;
	}
	
	public function renderTemplateFile($context = array()) {
		extract($context);
		ob_start();
		
		$this->log("About to include template name, {$this->name}.");
		$file = $this->pathToTemplate($this->name);
		$this->log("About to include template file, {$file}.");
		require $file;
		
		$content = ob_get_contents();
		ob_end_clean();
		
		
		return $content;
	}
	
	public function __toString() {
		return $this->render();
	}
	
	
	
	
	public static function renderTemplate($name,$vars) {
		$template = new Template($name);
		
		return $template->render($vars);
	}
	
	
	
	
	
	
	
	public function pathToTemplate($name) {
		
		$paths = $this->getPaths();
		$paths[]= get_theme_path();
		
		$search = array_map(function($item) use($name) {
			return $item . "/" . $name . self::TEMPLATE_EXTENSION;
		},$paths);
		
		$searchPaths = implode(",", $search);


		$found = array_values(array_filter($search,function($file) {
			return file_exists($file);
		}));
		
		if(!count($found) > 0) throw new \Exception("TEMPLATE_ERROR: Template $name could not be found at: " . $searchPaths);
		
		
		return $found[0];
	}
	
	
	public static function exists($name){
		return file_exists(self::pathToTemplate($name));
	}
	
	public function bind($context, $value = null) {
		if(!is_array($context) && !is_object($context)) {
			$this->context += array($context => $value);
		}
		else if(is_object($context) && in_array("IRenderable",class_implements($context))) {
			$this->context += $context->getContext();
		}
		else $this->context += $context;
		
		return $this;
	}
	

	
	/**
	 * Load a template by name; also a shortcut to get the scripts/styles, too.
	 */
	public static function loadTemplate($name) {
		
		return new Template($name);
	}
	
	public static function fromString($string) {
		$tpl = new Template();
		$tpl->setContent($string);
		
		return $tpl;
	}
	// Save some log messages.
	//  Might be of interest later.
	protected function log($msg) {
		if(self::DEBUG) print $msg;
		$this->log []= $msg;
	}	
	
}