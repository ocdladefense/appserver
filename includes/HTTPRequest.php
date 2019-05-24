<?php
class HTTPRequest
{
	private $handle = null;
	private $response = null;
	private $params = array();
	private $status; 
	private $errorString = null;
	private $errorNum = null;
	private $headers = array();
	private $requestType = "GET";
	 
	public function __construct($endpoint) 
	{
		// Return a handle to a process that can make an HTTP Request.
		$this->handle = curl_init($endpoint);
	}


	// Set our HTTP Request parameters.
	// $params = "code=" . $code . "&grant_type = authorization_code&client_id=" 
	//. CLIENT_ID. "&client_secret=" . CLIENT_SECRET. "&redirect_uri=" .urlencode(REDIRECT_URI);
	public function setParams($p) 
	{
	  // name/value pairs
	  // each name/value pair is separate by ampersand
	  // each name/value pair is set by an `=` sign
	  if(is_array($p))
	  {
		$_params = array();
		foreach($p as $key=>$value)
		{
			$_params[] = $key ."=".$value;
		}		
		$this->params = implode('&',$_params);
	  }
	  else
	  {
		  $this->params = $p;
	  }

	}
	public function setPost()
	{
		$this->requestType = "POST";

	}
	public function setPatch()
	{
		$this->requestType = "PATCH";
	}
	public function setDelete()
	{
		$this->requestType = "DELETE";
	}
	public function getRequestType()
	{
		return $this->requestType;
	}
	public function setOptions($params)
	{
		// Set various options for our HTTP Request.
		curl_setopt($this->handle, CURLOPT_HEADER, false);
		curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, true);
		if($this->getRequestType() == "POST")
		{
			curl_setopt($this->handle, CURLOPT_POST, true);
			curl_setopt($this->handle, CURLOPT_POSTFIELDS, $params);
		}
		if($this->getRequestType() == "PATCH")
		{
			curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "PATCH");
			curl_setopt($this->handle, CURLOPT_POSTFIELDS, $params);
		}
		if($this->getRequestType() == "DELETE")
		{
			curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, "DELETE");
			curl_setopt($this->handle, CURLOPT_RETURNTRANSFER, false);
		}

		if(count($this->headers)>0)
		{
			curl_setopt($this->handle, CURLOPT_HTTPHEADER, $this->headers);
		}
	}
	public function addHeaders($header)
	{
		$this->headers[] = $header; 
	}
	public function ignoreSSLVerification()
	{
		//Ignore the SSL vaification
		// https://curl.haxx.se/libcurl/c/CURLOPT_SSL_VERIFYPEER.html
		curl_setopt($this->handle,CURLOPT_SSL_VERIFYHOST, false); 
		curl_setopt($this->handle,CURLOPT_SSL_VERIFYPEER, false);
	}

	public function makeHTTPRequest()
	{
		$this->setOptions($this->params);
		$this->ignoreSSLVerification();
		// Make the actual HTTP Request AND it returns a HTTP Response.

		$this->response = curl_exec($this->handle);
		$hResponse = new HTTPResponse($this->response);
		$this->status = curl_getinfo($this->handle, CURLINFO_HTTP_CODE);

		if($this->status != 200)
		{
			$this->errorString = curl_error($this->handle);
			$this->errorNum = curl_errno($this->handle);
		}
		$this->closeHTTPConnection();

		return $hResponse;	
	}

	public function getStatus()
	{
		// Returns the status, e.g., 404 Not Found, 500 Internal Server Error of our HTTP Response.

		return $this->status;
	}
	public function closeHTTPConnection()
	{
		// Closing the HTTP connection.
		curl_close($this->handle);
	}
	public function getError()
	{
		return $this->errorString;
	}

	public function getErrorNum()
	{
		return $this->errorNum;
	}

	public function success()
	{
		return $this->status == 200;
	}
}// end HTTPRequest class
