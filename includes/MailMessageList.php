<?php





class MailMessageList {

	private $list = array();


	public function add($message) {
		$this->list []= $message;
	}

	public function clear() {
		$this->list = array();
	}

	public function getMessages() {
		return $this->list;
	}

}