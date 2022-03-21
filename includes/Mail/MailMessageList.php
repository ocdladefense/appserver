<?php





class MailMessageList implements IteratorAggregate {

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

    public function getIterator() {
        return new ArrayIterator($this->list);
    }
}