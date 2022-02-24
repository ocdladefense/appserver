<?php

abstract class Handler {


	protected $output;
	
	
	
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
	protected abstract function getOutput();
	
	
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


		$handlers = array(
			"MailMessage" => "HtmlEmailHandler",
			"Http\HttpResponse" => "HttpResponseHandler",
			"HtmlDocumentHandler" => "HtmlDocumentHandler",
			"Exception" => "HtmlErrorHandler",
			"File\File" => "ApplicationFileHandler",
			"String" => "StringHandler"
		);

		$type = gettype($output);
		$class = "object" === $type ? get_class($output) : ucfirst($type); // title case to find the appropriate handler.

		// Use a combination of accept and the route's content-type to
		// determine the most appropriate 
		$accept = $req->getHeader("Accept");
		$contentType = $route["content-type"];


		// $handler = self::fromType($req, $route, $output); // old call
		// new call
		$mimeType = $route["content-type"];
		$name = $handlers["String"];
		$handler = new $name($output, $mimeType);
		$handler->setAccept($accept);
		$handler->setContentType($contentType);


		return $handler;
	}



	/**
	 * HTTP Content Negotiation.
	 * 
	 * For requests with multiple Accept: values, the Route has the ultimate say in
	 * the resource representation.  But we also consult the Accept: header to see if we can 
	 * return the client's preferred representation.
	 */
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
}



