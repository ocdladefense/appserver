<?php

abstract class Handler {


	protected $output;
	
	
	
	protected $mimeType;
	
	
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


	public static function fromType($output, $mimeType = null) {

		// For a full HTML page
		// Render the HTML template and inject content to 
		//  be the body of the page.
		
		if(is_object($output) && get_class($output) == "Http\HttpResponse") {

			$handler = new HttpResponseHandler($output, $mimeType);
		}
		
		else if($mimeType == null || $mimeType == Http\MIME_TEXT_HTML) {

			$handler = is_object($output) && get_class($output) == "Exception" ?
					new HtmlErrorHandler($output, $mimeType) :
					new HtmlDocumentHandler($output, $mimeType);

		} else if($mimeType == Http\MIME_TEXT_HTML_PARTIAL) {

			$handler = new HtmlStringHandler($output, $mimeType);

		} else if( is_object($output) && get_class($output) == "File\File") {

			$handler = new ApplicationFileHandler($output, $mimeType);
			
		} else if($mimeType == Http\MIME_APPLICATION_JSON) {

			$handler = is_object($output) && (is_subclass_of($output, "Exception") || get_class($output) == "Exception" || get_class($output) == "Error") ?
				new JsonErrorHandler($output, $mimeType) :
				new JsonHandler($output, $mimeType);
	 
		} else {
			$handler = new DefaultHandler($output, $mimeType);
		}
	
		return $handler;
	}
}