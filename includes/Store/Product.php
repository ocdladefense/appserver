<?php

function getProduct($productId){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $prodQuery = sprintf("SELECT Id, Name FROM Product2 WHERE Id = '%s'",$productId);
    $product = $salesforce->createQueryFromSession($prodQuery);
    return $product["records"][0];
}
function getFirstProduct(){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $product = $salesforce->createQueryFromSession("SELECT Id, Name FROM Product2 LIMIT 1");
    return empty($product["records"][0]) ? false: $product["records"][0];
}

function getProductFromMedia($mediaId){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $prodQuery = sprintf("SELECT Id, Name, Media__c FROM Product2 WHERE Media__c = '%s'",$mediaId);
    $product = $salesforce->createQueryFromSession($prodQuery);
    return $product["records"][0];
}
function getPricebookEntries($productIds){
    $productIds = is_array($productIds)?$productIds:array($productIds);
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $productIds = implode("','",$productIds);
    $pricebookQuery = sprintf("SELECT Id from PricebookEntry WHERE Product2Id IN ('%s')",$productIds);
    $pricebookEntry = $salesforce->createQueryFromSession($pricebookQuery);
    return $pricebookEntry["records"];
}
function getAccount($contactId){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $account = $salesforce->createQueryFromSession("select AccountId from Contact where id = '".$contactId."'");
    return $account["records"][0];
}
function getOpportunity($accountId){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $opportunity = $salesforce->createQueryFromSession("SELECT Id, Amount, Name, StageName, Description,AccountId FROM Opportunity WHERE AccountId = '".$accountId."'");
    return !isset($opportunity["records"][0]) ? false : $opportunity["records"][0];
}
function loadCartItems($oppId){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $items = $salesforce->createQueryFromSession("SELECT Id,Description,ListPrice,Product2Id,ProductCode,Quantity,UnitPrice,TotalPrice FROM OpportunityLineItem WHERE OpportunityId = '".$oppId."'");
    return $items["records"];
}
function deleteCartItem($productLineId){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $result = $salesforce->deleteRecordFromSession("OpportunityLineItem",$productLineId);
    return $result == true?true:false;
}

function deleteCartItemRequest($lineId) {
    // $customerId = $_SESSION["customerId"] = TEST_CONTACT_ID;
    // global $oauth_config;
    $force = new Salesforce();
    $oName = "OpportunityLineItem";
    $id = $lineId;
    $req = $force->getDeleteRequest($oName, $id, "na111.salesforce.com");

    var_dump($req);
    
    
    $config = array(
        // "cainfo" => null,
        // "verbose" => false,
        // "stderr" => null,
        // "encoding" => '',
        "returntransfer" => true,
        // "httpheader" => null,
        "useragent" => "Mozilla/5.0",
        // "header" => 1,
        // "header_out" => true,
        "followlocation" => true,
        "ssl_verifyhost" => false,
        "ssl_verifypeer" => false
    );

    $http = new Http\Http($config);

    $resp = $http->send($req);
    
    //print_r($http->getSessionLog());
    $customerId = $_SESSION["customerId"] = TEST_CONTACT_ID;

        $cart = $_SESSION["cart"] == null ? Salesforce\ShoppingCart::loadCart($customerId):$_SESSION["cart"];
        return $cart;
    exit;
    // $cart = $_SESSION["cart"] == null ? ShoppingCart::loadCart($customerId):$_SESSION["cart"];
    // return $cart->deleteItemLine($productLineId)?$cart:false;

}