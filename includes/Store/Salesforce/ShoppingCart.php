<?php


namespace Salesforce;
use Http\IJson;
use Http\HttpResponse;
use \Salesforce;

class ShoppingCart  implements \Http\IJson {

    private $id;
    private $items;
    private $total;
    private $currency;

    private static $salesforce = null;

    public function __construct($currency = "USD") {
        global $oauth_config;
        //$this->total = $total;
        $this->currency = $currency;
        self::$salesforce = new Salesforce($oauth_config);
    }

    public function refresh() {
        return $this->items;
    }

    //Setters
    public function setTotal($total){
        $this->total = $total == null ? 0 : $total;
        //$this->total = $total;
    }

    public function setCurrency($currency){
        $this->currency = $currency;
    }

    //Getters

    public function getItems() {
        return $this->items;
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

    private function setId($id){
        $this->id = $id;
    }
    public function getId(){
        return $this->id;
    }
    public function loadItems(){
        $this->items = loadCartItems($this->id);
    }
    public function deleteProductLine($productLineId){
        $this->items = deleteCartItem($productLineId);
    }
    public static function newFromCustomerId($customerId){
        global $oauth_config;
        //accountId,customerId,OppName
        //query for account Id
        //session for accountId
        $cartBody = array(
            "AccountId" => TEST_ACCOUNT_ID,
            "Name" => "My Shopping Cart",
            "StageName" => "Draft",
            "CloseDate" => "2020-12-15"
        );
        $salesforce = new Salesforce($oauth_config);
        
		$response = $salesforce->createRecordFromSession("Opportunity",json_encode($cartBody));
        //return $response;
        if($response["success"] != true){
            return false;
        }
        $cart = new ShoppingCart();
        $cart->setId($response["id"]);

        return $cart;
    }
    public function addProduct($productId){
        $productId = is_array($productId) ? $productId["Id"] : $productId;
        $pricebookEntry = getPricebook($productId);
        global $oauth_config;
        $salesforce = new Salesforce($oauth_config);
        
        $item = array(
            "Quantity"=>1,
            "PricebookEntryId"=>$pricebookEntry["Id"],
            "OpportunityId"=>$this->id,
            "TotalPrice"=>20
        );
        $response=$salesforce->createRecordFromSession("OpportunityLineItem",json_encode($item));
        if($response["success"] != true){
            throw new \Exception("could not add the product to the cart");
        }
        ++$this->items;
        return true;
    }
    public static function getFromCustomerId($customerId){
        $account = getAccount($customerId);
        $opportunity = getOpportunity($account["AccountId"]);
       
        $cart = new ShoppingCart();
        $cart->setId(false === $opportunity ? null : $opportunity["Id"]);
        $cart->loadItems();
        $cart->setTotal($opportunity["Amount"]);

        return $cart;
    }

    public static function loadCart($customerId){
        if (empty($customerId)){
            throw new \Exception("customer Id is empty");
        }
		$cart = $customerId == null ? ShoppingCart::newFromCustomerId($customerId) : ShoppingCart::getFromCustomerId($customerId);
		if ($cart->getId() == null){
			//check for 0 and expired
			throw new \Exception("could not create cart");
        }
		return $cart;
	}

    function addItem($productId,$quantity = 1){		
		try {
			$product = getProduct($productId);

			$this->addProduct($product);
			return array(
				"product" => $product["Name"],
				"success" => true
			);
		} catch (\Exception $e) {
			$response = new HttpResponse();
			$response->setStatusCode(400);
			$response->setBody("error trying to add ".$productId." to cart: ".$e->getMessage());
			return $response;
		}
    }
    function deleteItemLine($productLineId){
		try {
			$this->deleteProductLine($productLineId);
			return array(
				"productLineId" => $productLineId,
				"success" => true
			);
		} catch (\Exception $e) {
			$response = new HttpResponse();
			$response->setStatusCode(400);
			$response->setBody("error trying to remove ".$productLineId." from cart: ".$e->getMessage());
			return $response;
		}
    }

    public function toJson(){
        return json_encode(array(
            "items" => $this->getItems(),
            "id" => $this->getId(),
            "currency" => $this->getCurrency(),
            "total" => $this ->getTotal()
         ));
    }

}