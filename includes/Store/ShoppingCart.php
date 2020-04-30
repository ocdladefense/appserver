<?php
namespace ClickpdxStore;
class ShoppingCart {

    private $items;
    private $total;

    public function __construct() {

    }

    public function refresh() {
        return $this->items;
    }

    //Setters
    public function setTotal($total){
        $this->total = $total;
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
        // $this->total = 950;
        return $this->total;
    }
}