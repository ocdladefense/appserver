<?php


namespace Salesforce;

use Exception;
use Http\IJson;
use Http\HttpResponse;
use \Salesforce;

class ShoppingCart  implements \Http\IJson {

    private $id;
    private $items;
    private $total;
    private $currency;
    private $error;

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

    public function setError($error){
        $this->error = $error;
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

    public function getError(){
        return $this->error;
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
        if(!deleteCartItem($productLineId)){
            throw new \Exception("Error Deleting Item");
        }
        //reload all items or just delete the correct one by passing value
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

    /*
    public function addProducts($productsIds){
        //check for array
        $pricebookEntries = getPricebookEntries($productIds);
        global $oauth_config;
        $salesforce = new Salesforce($oauth_config);
        
        $item = array(
            "Quantity"=>1,
            "PricebookEntryId"=>$pricebookEntry["Id"],
            "OpportunityId"=>$this->id,
            "TotalPrice"=>20
        );
    }
    */
    public function updateProdQty($productIds){
        global $oauth_config;
        $salesforce = new Salesforce($oauth_config);
    }
    
    public function addProduct($productId , $quantity = 1){
        $productId = is_array($productId) ? $productId["Id"] : $productId;
        $pricebookEntry = getPricebookEntries($productId,$this->id)[0];
        global $oauth_config;
        $salesforce = new Salesforce($oauth_config);
        //
        $item = array(
            "Quantity"=> $quantity,
            "PricebookEntryId"=> $pricebookEntry["Id"],
            "OpportunityId"=> $this->id,
            "TotalPrice"=>$pricebookEntry["UnitPrice"]*$quantity
        );//Check for "UnitPrice" instead
        $response=$salesforce->createRecordsFromSession("OpportunityLineItem",$item);
        if($response["success"] != true){
            throw new \Exception("could not add the product to the cart");
        }
        ++$this->items;
        return true;
    }

    public function addProducts($products, $quantity = 1, $specialPrice = null){
        global $oauth_config;
        $salesforce = new Salesforce($oauth_config);
        if((!is_string($products) || $quantity <= 0 || $specialPrice < 0) && (!is_array($products))){
            throw new \Exception("Error calling the function");
        }
        //if($products)
        
        if(is_array($products)){
            try {
                $response = $salesforce->createRecordsFromSession("OpportunityLineItem",$products);
            } catch (\Throwable $th) {
                throw new Exception($th->getMessage());
                //json body undecoded
            }
        }else{
            $pricebookEntry = getPricebookEntries($products,$this->id);
            $item = array(
                "Quantity"=> $quantity,
                "PricebookEntryId"=> $pricebookEntry["Id"],
                "OpportunityId"=> $this->id,
                "TotalPrice"=>$pricebookEntry["UnitPrice"]*$quantity
            );
            if($specialPrice != null){
                $item["TotalPrice"] = $specialPrice;
            }
            $response=$salesforce->createRecordsFromSession("OpportunityLineItem",$item);
            //check for price book
        }
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
			$product = getProducts($productId);

			$this->addProduct($product[0],$quantity);
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
    function addItems($items){
        if(!is_array($items) || empty($items)){
            throw new Exception ("Items are null or empty");
        }
        $productIds = array();
        //$productQts = array();
        //$specialPrices = array();
        //$addedItems = array();
        foreach($items as $item){
            array_push($productIds,$item["Id"]);
            //array_push($productQts,$item["Quantity"]);
        }
        
        $priceBook = getPricebookEntries($productIds,$this->id);

        $addProdPrice = function ($item,$index) use($priceBook){
            $item["PricebookEntryId"] = $priceBook[$index]["Id"];
            $item["OpportunityId"] = $this->id;
            $item["UnitPrice"] = $priceBook[$index]["UnitPrice"];
            $item["Quantity"] = $item["Quantity"];
            unset($item["Id"]);
            return $item;
        };
        $items = array_map($addProdPrice,$items,array_keys($items));


        $this->addProducts($items);
        $items = array_map(function($item,$index,$productInfo){
            $item[$index]["Name"] = $productInfo[$index]["Name"];
            unset($item["OpportunityId"]);
            unset($item["PricebookEntryId"]);
            return $item;
        },$items,array_keys($items),getProducts($productIds));

		return array(
		    "itemsAdded" => $items,
		    "success" => true
		);
    }

    function deleteItemLine($productLineId){
        $this->deleteProductLine($productLineId);
        $this->items = loadCartItems($this->id);
		return true;
    }

    public function toJson(){
        return json_encode(array(
            "items" => $this->getItems(),
            "id" => $this->getId(),
            "currency" => $this->getCurrency(),
            "total" => $this ->getTotal(),
            "errors" => $this->getError()
         ));
    }

}