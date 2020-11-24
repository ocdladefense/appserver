<?php

function getProduct($productId){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $prodQuery = sprintf("SELECT Id, Name FROM Product2 WHERE Id = '%s'",$productId);
    $product = $salesforce->createQueryFromSession($prodQuery);
    return $product["records"][0];
}

function getProducts($Ids){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);

    if (is_array($Ids)){
        $products = $salesforce->createQueriesFromSession("Product2",$Ids,array("id","name"));
        return $products;
    }
    $prodQuery = sprintf("SELECT Id, Name FROM Product2 WHERE Id = '%s'",$Ids);
    $product = $salesforce->createQueryFromSession($prodQuery);
    return $product["records"][0];
}
function getFirstProduct(){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $product = $salesforce->createQueryFromSession("SELECT Id, Name FROM Product2 LIMIT 1");
    return empty($product["records"][0]) ? false: $product["records"][0];
}
function getAllProducts($limit = 0){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $prodsQuery = "SELECT Id, Name FROM Product2";
    $prodsQuery = $limit ?  $prodsQuery :  $prodsQuery . " LIMIT " . $limit;
    $product = $salesforce->createQueryFromSession($prodsQuery);
    return empty($product["records"]) ? false: $product["records"];
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
    $account = $salesforce->createQueryFromSession(sprintf("SELECT AccountId FROM Contact WHERE Id = '%s'",$contactId));
    return $account["records"][0];
}
function getOpportunity($accountId){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $oppQuery = sprintf("SELECT Id, Amount, Name, StageName, Description,AccountId FROM Opportunity WHERE AccountId = '%s'",$accountId);
    $opportunity = $salesforce->createQueryFromSession($oppQuery);
    return !isset($opportunity["records"][0]) ? false : $opportunity["records"][0];
}
function loadCartItems($oppId){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $lineItemQuery = sprintf("SELECT Id,Description,ListPrice,Product2Id,ProductCode,Quantity,UnitPrice,TotalPrice
         FROM OpportunityLineItem WHERE OpportunityId = '%s'",$oppId);
    $items = $salesforce->createQueryFromSession($lineItemQuery);
    return $items["records"];
}

function loadToCart($products){
    
}

function deleteCartItem($productLineId){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $result = $salesforce->deleteRecordFromSession("OpportunityLineItem",$productLineId);
    return $result == true?true:false;
}