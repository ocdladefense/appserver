<?php
class HTTPResponse
{
    private $body = null;
    private $headers = array();

    public function __construct($html)
    {
         $this->body = $html;
    }
    public function getBody()
    {
        return $this->body;
    }
    public function getPhpArray()
    {
        // Parsing the HTTP Response; by parsing we just mean the data has a known format and we can retrieve certain things from the Response.
		return json_decode($this->body, true);
    }
    public function setBody()
    {
        
    }
    public function __toString()
    {
        return $this->body;
    }
}
?>