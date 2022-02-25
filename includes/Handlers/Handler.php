<?php


use Http\MediaRange;

abstract class Handler {


	protected $output;
	
	
	protected static $handlers = array(
		"MailMessage" => "HtmlEmailHandler",
		"Http\HttpResponse" => "HttpResponseHandler",
		"HtmlDocumentHandler" => "HtmlDocumentHandler",
		"Exception" => "HtmlErrorHandler",
		"File\File" => "ApplicationFileHandler",
		"String" => "StringHandler"
	);

	protected $contentTypes = array();
	

	protected $mimeType;
	
	

	private $accept;


	private $contentType;


	
	/**
	 * @method getOutput
	 *
	 * @description
	 *   Format the output consistent with the specified handler
	 *    instance.  Typically this will be a string data type.
	 */
	// protected abstract function getOutput();
	
	
	/**
	 * @method getHeaders
	 *
	 * @description
	 *    Get any HttpHeaders consistent with the mime-type 
	 *     of this data.
	 */
	protected abstract function getHeaders();


	public function setAccept($accept) {

		$this->accept = $accept;
	}

	public function setContentType($type) {

		$this->contentType = $type;
	}

	public function getAccept() {

		return $this->accept;
	}
	public function getContentType() {

		return $this->contentType;
	}


	public function getRepresentations(){

		return $this->contentTypes;
	}

	public static function getAcceptableRepresentationMimeTypes($ranges, $contentTypes) {

		$includesMime = function($quality, $acceptedMime) use($contentTypes) {

			foreach($contentTypes as $type) {

				$range = new MediaRange($acceptedMime);
				$mime = new MediaRange($type);

				if($range->includes($mime)) {
					return true;
				}
			}
			return false;
		};

		return array_keys(array_filter($ranges, $includesMime, ARRAY_FILTER_USE_BOTH));
	}




	public static function isCompatible($mediaRange, $contentType) {
		// $accept = new MediaRange($range);
		// $content = new MediaRange
	} 
	/*
	"boolean"
	"integer"
	"double" (for historical reasons "double" is returned in case of a float, and not simply "float")
	"string"
	"array"
	"object"
	"resource"
	"resource (closed)" as of PHP 7.2.0
	"NULL"
	"unknown type"
	*/
	// See also, https://www.php.net/manual/en/function.gettype.php
	public static function getRegisteredHandler(\Http\HttpRequest $req, $route, $output) {

		$type = gettype($output);
		$name = "object" === $type ? get_class($output) : ucfirst($type); // title case to find the appropriate handler.

		$mimeType = $route["content-type"];
		$class = self::$handlers[$name];
		$handler = new $class($output,$mimeType);

		return $handler;
	}

	public function getOutputMethodName($contentType = "text/html") {
		$tmp = preg_split("/\/|\+/",$contentType);
		$parts = array_map(function($part) { return ucfirst($part);}, $tmp);
		$name = implode("",$parts);
		$method = "get{$name}";

		return $method;
	}

	public function getOutput($contentType = "text/html") {

		$method = $this->getOutputMethodName($contentType);

		// If the content type uses an "*" to indicate any subtype replace it with the word "Any"
		// You cant name a function using "*".  (ex: function text*())
		if(strpos($method, "*") !== false) $method = str_replace("*", "Any", $method);
		
		return $this->{$method}();
	}

	

	/**
	 * HTTP Content Negotiation.
	 * 
	 * For requests with multiple Accept: values, the Route has the ultimate say in
	 * the resource representation.  But we also consult the Accept: header to see if we can 
	 * return the client's preferred representation.
	 
	public static function fromType(\Http\HttpRequest $req, $route, $output) {

		$mimeType = $route["content-type"];


		if(is_object($output) && get_class($output) == "MailMessage"){

			$handler = new HtmlEmailHandler($output);
		}
		else if($mimeType == "application/xml")
		{
			$handler = new XmlHandler($output, $mimeType);
		}	
		else if(is_object($output) && get_class($output) == "Http\HttpResponse")
		{
			$handler = new HttpResponseHandler($output, $mimeType);
		}
		else if($mimeType == null || $mimeType == Http\MIME_TEXT_HTML)
		{
			$handler = is_object($output) && get_class($output) == "Exception" ?
					new HtmlErrorHandler($output, $mimeType) :
					new HtmlDocumentHandler($output, $mimeType);
		}
		else if($mimeType == Http\MIME_TEXT_HTML_PARTIAL)
		{
			$handler = new HtmlStringHandler($output, $mimeType);
		}
		else if( is_object($output) && get_class($output) == "File\File")
		{
			$handler = new ApplicationFileHandler($output, $mimeType);
		} 
		else if($mimeType == Http\MIME_APPLICATION_JSON)
		{
			$handler = is_object($output) && (is_subclass_of($output, "Exception") || get_class($output) == "Exception" || get_class($output) == "Error") ?
				new JsonErrorHandler($output, $mimeType) :
				new JsonHandler($output, $mimeType);
		}
		else
		{
			$handler = new DefaultHandler($output, $mimeType);
		}
	

		return $handler;
	}
	*/
}



