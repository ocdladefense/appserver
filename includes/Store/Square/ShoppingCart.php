<?php
namespace Square;

class ShoppingCart {

    private $items;
    private $total;
    private $currency;

    public function __construct($currency = "USD") {
        $this->total = $total;
        $this->currency = $currency;
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


    /**
     * 
     */
    public static function fromParams($params){
		$cart = new ShoppingCart();
		$cart->setTotal($params->total);
		$cart->setCurrency($params->currency ?: "USD");

		return $cart;
	}

}