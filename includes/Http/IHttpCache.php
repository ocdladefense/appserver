<?php

namespace Http;

interface IHttpCache {


	public function exists($key);
	
	public function put($key,$resp);
	
	public function get($key);


}