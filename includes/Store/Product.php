<?php

function getProduct($productId){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $prodQuery = sprintf("SELECT Id, Name FROM Product2 WHERE Id = '%s'",$productId);
    $product = $salesforce->createQueryFromSession($prodQuery);
    return $product["records"][0];
}

function getProductFromMedia($mediaId){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $prodQuery = sprintf("SELECT Id, Name, Media__c FROM Product2 WHERE Media__c = '%s'",$mediaId);
    $product = $salesforce->createQueryFromSession($prodQuery);
    return $product["records"][0];
}
function getPricebook($productId){
    global $oauth_config;
    $salesforce = new Salesforce($oauth_config);
    $pricebookQuery = sprintf("SELECT Id from PricebookEntry WHERE Product2Id = '%s'",$productId);
    $pricebookEntry = $salesforce->createQueryFromSession($pricebookQuery);
    return $pricebookEntry["records"][0];
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
    $opportunity = $salesforce->createQueryFromSession("select Id, Name from Opportunity where AccountId = '".$accountId."'");
    return !isset($opportunity["records"][0]) ? false : $opportunity["records"][0];
}




