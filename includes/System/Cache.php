<?php
/**
 * 
 */



 namespace System;


 class Cache {


	// Store an item in the cache.  This could be any object.
	public function put($key, $object) {
		$bucket = $key;
		$path = CACHE_DIR . "/public/{$bucket}";

		file_put_contents($path, $object);
	}


	
	public function get($key = "ors.ors813.html") {
		list($bucket,$object) = explode($key);
		$dir = CACHE_DIR . "/public/{$bucket}";

		return file_get_contents($dir . "/{$object}");
	}

 }