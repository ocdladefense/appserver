<?php

namespace Http;

	const HTTP_METHOD_GET = "GET";
	
	const HTTP_METHOD_POST = "POST";
	
	const HTTP_METHOD_PUT = "PUT";
	
	const HTTP_METHOD_DELETE = "DELETE";
	
	const MIME_TEXT_HTML = "text/html; charset=utf-8";
	
	const MIME_APPLICATION_JSON = "application/json; charset=utf-8";
	
	const MIME_TEXT_JAVASCRIPT = "text/javascript";
	
	function formatResponseBody($content, $contentType) {

		if(strpos($contentType,"json")) {
				if(is_array($content) || is_object($content)) {
						$out = json_encode($content);
				}
				else {
						$out = json_encode(array("content" => $content));
				}  
		}
		else {
				$out = $content;
		}
		
		return $out;
	}