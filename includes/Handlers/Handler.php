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
		if($mimeType == null || $mimeType == Http\MIME_TEXT_HTML) {

			$handler = new HtmlDocumentHandler($output, $mimeType);

		} else if($mimeType == Http\MIME_TEXT_HTML_PARTIAL) {

			$handler = new HtmlStringHandler($output, $mimeType);
			
		} else if($mimeType == Http\MIME_APPLICATION_JSON) {

			$handler = new JsonHandler($output, $mimeType);
	 
		} else {
			$handler = new DefaultHandler($output, $mimeType);
		}
	
		return $handler;
	}
}