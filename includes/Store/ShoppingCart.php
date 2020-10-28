<?php

use Http\IJson;

class ShoppingCart  implements IJson {

    private $Id;
    private $items;
    private $total;
    private $currency;
    private static $oauth_config = array(
		"oauth_url" => SALESFORCE_LOGIN_URL,
		"client_id" => SALESFORCE_CLIENT_ID,
		"client_secret" => SALESFORCE_CLIENT_SECRET,
		"username" => SALESFORCE_USERNAME,
		"password" => SALESFORCE_PASSWORD,
		"security_token" => SALESFORCE_SECURITY_TOKEN,
		"redirect_uri" => SALESFORCE_REDIRECT_URI
        );
    private $salesforce = null;

    public function __construct($currency = "USD") {
        $this->total = $total;
        $this->currency = $currency;
        $this->salesforce = new Salesforce(self::$oauth_config);
    }

    public function refresh() {
        return $this->items;
    }

    //Setters
    public function setTotal($total){
        $this->total = $total;
    }

    public function setCurrency($currency){
        $this->currency = $currency;
    }

    //Getters

    public function getItems() {
        //return $this->items;
        return array(
            array(
                "name" => "Fooby",
                "productId" => "00001",
                "productPrice" => 20.00,
                "quantity" => 400
            )
        );
    }

    public function getTotal(){
        return $this->total;
    }

    public function getCurrency(){
        return $this->currency;
    }
    public function calculateTotal(){
        $itemsArray = $this->getItems(); 
        $total;

        foreach($itemsArray as $item){
            $total += $item["productPrice"]; 
        }

        return $total;
    }


    /**
     * 
     */
    public static function fromParams($params){
		$cart = new ShoppingCart();
		$cart->setTotal($params->total);
		$cart->setCurrency($params->currency ?: "USD");

		return $cart;
    }

    private function setId($Id){
        $this->Id = $Id;
    }
    public function getId(){
        return $this->Id;
    }
    public static function newFromCustomerId($customerId){
        //accountId,customerId,OppName
        //query for account Id
        //session for accountId
        $cartBody = array(
            "AccountId" => TEST_ACCOUNT_ID,
            "Name" => "My Shopping Cart",
            "StageName" => "Draft",
            "CloseDate" => "2020-12-15"
        );
        $salesforce = new Salesforce(self::$oauth_config);
        
		$response = $salesforce->createRecordFromSession("Opportunity",json_encode($cartBody));
        //return $response;
        if($response["success"] != true){
            return false;
        }
        $cart = new ShoppingCart();
        $cart->setId($response["id"]);

        return $cart;
    }
    public static function addProduct($productId){
        $salesforce = new Salesforce(self::$oauth_config);
        $PricebookEntry = $salesforce->createQueryFromSession("select Id from PricebookEntry where Product2Id = '".$productId."'");
        $item = array(
            "Quantity"=>1,
            "PricebookEntryId"=>$PricebookEntry["id"],
            "OpportunityId"=>$_SESSION["cartId"]
        );
        $salesforce->createRecordFromSession("OpportunityLineItem",json_encode($item));
    }
    public static function getFromCustomerId($customerId){
        $salesforce = new Salesforce(self::$oauth_config);
        $response = $salesforce->createQueryFromSession("select Id, Name from Opportunity where AccountId = '".$customerId."'");
        $cart = new ShoppingCart();
        $cart->setId($response["id"]);
        return $cart;
    }

    public function toJson(){
        
    }

}