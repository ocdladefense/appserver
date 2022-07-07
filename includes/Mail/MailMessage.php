<?php





class MailMessage {


	private $to = "";


	private $subject = "";

	private $title = "";


	private $body = "";


	private $headers = null;
	



	public function __construct($to = ""){
		$this->to = $to;
	}



	public function setTitle($title) {
		$this->title = $title;
	}

	public function getTitle() {

		return $this->title;
	}



    public static function isMailMessage($object){

        return is_object($object) && (get_class($object) === "MailMessage" || is_subclass_of($object, "MailMessage", false) || get_class($object) === "MailMessageList");

    }


	public function getTo() {
		return !empty($this->to) ? $this->to : $this->headers->getValue("To");
	}

  
	public function getSubject() {
		return !empty($this->subject) ? $this->subject : $this->headers->getValue("Subject");
	}

	public function getHeaders($format = false){

		return $format === true ? implode("\r\n", $this->headers->getList()) : $this->headers->getHeadersAsAssociativeArray();
	}

	public function getBody($format = false){

		return str_replace("\n", "\r\n", $this->body);
	}




	public function setTo($to) {
		$this->to = $to;
	}

	public function setSubject($subject) {
		$this->subject = $subject;
	}

	public function setBody($body){

		$this->body = $body;
	}
	
	public function setHeaders($headers){

		$this->headers = $headers;
	}


	public function __toString() {
		return $this->getBody();
	}
}