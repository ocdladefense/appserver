# appserver
A PHP application server

SYSTEM REQUIREMENTS

NOTE: ccapp requires php version >=5.6 and the php-curl extension

Apache config:
sudo a2enmod headers





# Instructions
# Using HTTP
Appserver has built-in classes to send HTTP Requests and receive HTTP Responses.

<code>
<?php 
use Http\HttpRequest as HttpRequest;
use Http\HttpResponse as HttpResponse;


// Fetch the New York Times homepage.
// "All the news that's fit to print."
$url = "https://nytimes.com";

// Init an http instance for sending requests;
// include any necessary configuration options.
$config = array();
$http = new Http($config);

// The request.
// See HTTP/1.1 Request(https://www.w3.org/Protocols/rfc2616/rfc2616.html) for more info.
$req = new HttpRequest($url);

// Sending the request returns a Response.
// See HTTP/1.1 Response(https://www.w3.org/Protocols/rfc2616/rfc2616.html) for more info.
$resp = $http->send($req);

// See what's in this message.
echo $resp->getBody();
</code>






INSTALLATION NOTES

Install composer
--> sudo apt install composer

Step #1 - Clone the repository

Step #2 - Composer, install the dependencies.
 --> cd ccapp
 --> composer update
 
Resolve dependencies
 --> For example, if php-curl is not installed, run:
    sudo apt-get install php7.2-curl

Step #3 - Run composer update (again)
 ubuntu@ip-172-31-22-87:/var/www/wordpress/ccapp$ composer update
Loading composer repositories with package information
Updating dependencies (including require-dev)
Package operations: 1 install, 0 updates, 0 removals
    Failed to download authorizenet/authorizenet from dist: The zip extension and unzip command are both missing, skipping.
Your command-line PHP is using multiple ini files. Run `php --ini` to show them.
    Now trying to download from source
  - Installing authorizenet/authorizenet (2.0.0): Cloning 7fa78e6397 from cache
Writing lock file
Generating autoload files
 
Step #4 - Create a config/config.php file
 --> copy the config-default.php file to config.php

Step #5 - Create an apache virtual host
 --> modify the DOCUMENT_ROOT setting appropriately
 
Step #6 - Create an .htaccess file
  --> modify the RewriteRule target path with a prevailing forward slash that is to be interpreted as being relative to the DOCUMENT_ROOT setting (above)

Step #7 - Test a route
 --> We're going to test "get-customer-payment-profile/{customerId}"
