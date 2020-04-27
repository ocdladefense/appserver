<?php
namespace ClickpdxStore;
class ShoppingCart {

    private $items;

    public function __construct() {

    }

    public function refresh() {
        return $this->items;
    }

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

    public function getPrice(){
        return 999.00;
    }
}