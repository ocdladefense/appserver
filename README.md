# Appserver
A PHP application server.  This application server can be used to demonstrate several RFCs, especially those concerned with HTTP, i.e., RFC 1945 (https://datatracker.ietf.org/doc/html/rfc1945). 

## Current changes
- OAuth::setSession - moved to CoreModule::setSession
- OAuth::getUser - moved to CoreModule::getUser
- OAuth::logout - moved to CoreModule::logout


- Repository: lib-salesforce-rest-api
 -- OAuth.php - pruned fo 3 static methods.
 -- Push new major version of lib-salesforce-rest-api?
    -- Probably not because modules are tied to version 2.x
 -- Push a new *minor version of lib-salesforce-rest-api?
    -- YES - this won't break existing modules.  Ug.


- Repository: lib-oauth
  -- Combine lib-oauth-config package (OAuth config classes) with OAuth.php from lib-salesforce-rest-api.

  



## System Requirements
1) Windows: Working WAMP Installation
2) Apache2
3) PHP >=7.3 with php-curl, php-xml-dom and others
4) MySQL server
5) Composer (https://getcomposer.org/)



## Composer
Composer is a package manager for PHP.  Appserver includes composer.json and composer-dev.json files that reference any required packages.  Composer can install these packages; typically you use composer update or composer install to download any required packages.  
- For development use the following:
- NOTE: Windows doesn't support the env macro; Windows users must forever copy the composer-dev.json file to composer.json, then run composer update.
- NOTE: for linux-flavored users: env COMPOSER=composer-dev.json composer update 






## INSTALLATION NOTES
### Description 
If WAMP is already installed you can confirm that you have a half-way decent environment by turning the Apache webserver on, locating your Apache document root and creating a "Hello World!" sample.php file.  Open it in a web browser by using the reserved "localhost" domain, i.e., http://localhost/path-to-simple-file/index.php.  If it works you're on the right track.


### Overview
We use the required applications to install an environment that this repo can run in.  As this is a PHP application, a LAMP/WAMP stack is required.  Installing an application like this involves configuring this environment, downloading any additional packages (or their dependencies), identifying the names and locations of configuration files, understanding how the configuration files change the environment, confirming that your configuration changes are effective and finally creating a hostname to reference the site in your preferred web browser.

### Configuration
PHP and Apache will likely need to be further configured, especially to load the latest version of mod_php (the PHP module for Apache), to change any necessary runtime settings for both Apache and PHP, and especially to configure the Apache document root for this application.

Once that's done we still need to configure the appserver itself.  Check config/ in this repo for the relevant settings and examples.

### Installation Steps
- The goal is to get something like c:\wamp64\www\appserver with the appropriate files so you can run http://localhost/appserver/test/1 in a web browser.


Install the Composer package manager:
- sudo apt install composer (linux)
- https://getcomposer.org/doc/00-intro.md#installation-windows

Step #1 - Clone this repository into your web root.

Step #2 - Composer - install the dependencies using a console/terminal application.
 - NOTE: VS Code comes pre-installed with a terminal
 --> cd appserver
 --> composer update
 
Resolve dependencies
 --> For example, if php-curl is not installed, run:
    sudo apt-get install php7.2-curl (linux) or enable the appropriate module using WAMP

Step #3 - Run composer update (again)
c:\wamp64\www\appserver $> composer update
Loading composer repositories with package information
 
Step #4 - Create a config/config.php file
 --> copy the config-example.php file to config.php

Step #5 - Create an apache virtual host
 --> modify the DOCUMENT_ROOT setting appropriately
 
Step #6 - Create an .htaccess file
  --> modify the RewriteRule target path with a prevailing forward slash that is to be interpreted as being relative to the DOCUMENT_ROOT setting (above)

Step #7 - Test a route
 --> Test one of the provided routes or clone the example module to get a simple route.
 - https://github.com/ocdladefense/appserver-test.git



## Specific Configurations
### Apache
Enable alias, directory, headers and any other necessary Apache modules.


### Using HTTP
Appserver has built-in classes to send HTTP Requests and receive HTTP Responses.


### use Http\HttpRequest as HttpRequest; 
### use Http\HttpResponse as HttpResponse;


Fetch the New York Times homepage.
"All the news that's fit to print."
  $url = "https://nytimes.com";

### Init an http instance for sending requests;
### include any necessary configuration options.
<code>$config = array();</code>
<code>$http = new Http($config);</code>

### The request.
### See HTTP/1.1 Request(https://www.w3.org/Protocols/rfc2616/rfc2616.html) for more info.
<code>$req = new HttpRequest($url);</code>

### Sending the request returns a Response.
### See HTTP/1.1 Response(https://www.w3.org/Protocols/rfc2616/rfc2616.html) for more info.
<code>$resp = $http->send($req);</code>

### See what's in this message.
<code>echo $resp->getBody();</code>

