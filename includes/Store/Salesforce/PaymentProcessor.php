<?php

namespace Salesforce;
use \Salesforce;

class PaymentProcessor {

    public function __construct() {

    }

    public function addCard($customerId){
        $salesforce = new Salesforce($this->oauth_config);
    }

	public function setInactiveCard($cardId){
		$salesforce = new Salesforce($this->oauth_config);
		$card = new \stdclass();
		$card->Status = "Inactive";
		return $salesforce->updateRecordFromSession("CardPaymentMethod",$cardId,json_encode($card));
	}
	public function deleteCard($cardId){
		$salesforce = new Salesforce($this->oauth_config);

		return $salesforce->deleteRecordFromSession("CardPaymentMethod",$cardId);
	}
}

