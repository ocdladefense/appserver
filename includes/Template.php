<?php


class Template {
  
  const DEBUG = false;
  
  
  // Default file extension for template files.
	const TEMPLATE_EXTENSION = ".tpl.php";
	
	
	// Name of this template; usually the name of a template.
	protected $name;
	
	
	// Context to be bound to this template.
	protected $context = array();
	
	
	// Path or paths associated with this template.
  protected $paths = array();
	

	// Messages to record status of this template.
	protected $log = array();
	
	
	protected $scripts = array();
	
	
	protected $styles = array();
	
	
	
	// Name is optional so we can use string literals to contruct a template.
	public function __construct($name = null) {
		$this->name = $name;
	}
	
	public function setContent($content) {
		$this->content = $content;
	}
		



	public function addPath($path) {
		$this->paths []= $path;
	}
	
	public function getPaths() {
		return $this->paths;
	}
	
	public function addStyles($styles) {
		$this->styles += $styles;
	}
	
	public function getStyles() {
		return $this->styles;
	}
	
	public function addScripts($scripts) {
		$this->scripts += $scripts;
	}
	
	public function getScripts() {
		return $this->scripts;
	}
	
	public static function isTemplate($obj) {
		return is_object($obj) && (get_parent_class($obj) == "Template" || get_class($obj) == "Template");
	}


	public function render($context = array()) {
		$context = count($context) === 0 ? $this->context : $context;
		
		return null == $this->content ? $this->renderTemplateFile($context) : $this->renderTemplateString($context);
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