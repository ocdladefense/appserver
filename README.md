# appserver
A PHP application server

SYSTEM REQUIREMENTS
Apache webserver
MySQL Server / MariaDB for car database access.

PHP >=5.6 and the php-curl extension

Apache config:
sudo a2enmod headers

## Setting up the "appserver" so that it works with the latest version of the "lib-oauth-config" package.
### Steps
1) In your terminal, checkout the development branch of the appserver repository.
2) Run the "git pull" command.
3) Run the "composer update" command.
4) In your config.php file, make sure that the configuration for the connected app that you are using follows the array structure outlined in the "Version 2.0" example in the "config-example.php" file. The configuration files are located in the "config" directory of the appserver.



INSTALLATION NOTES
## Description 
At least something to go on here for how to install this application. If WAMP is already installed you can confirm that you have a half-way decent environment by turning the Apache webserver on, locating your Apache document root and creating a "Hello World!" index.php file.  Open it in a web browser by using the reserved "localhost" domain, i.e., http://localhost/path-to-simple-file/index.php.  If it works you're on the right track.

## Required applications
- WAMP (so Apache2 web server, MySQL Server, PHP)
- Composer package manager (https://getcomposer.org/)
- This repository
- Web browser
- Text editor for updating configuration files.

## Overview
We use the required applications to install an environment that this repo can run in.  As this is a PHP application, a LAMP/WAMP stack is required.  Installing an application like this involves configuring this environment, downloading any additional packages (or their dependencies), identifying the names and locations of configuration files, understanding how the configuration files change the environment, confirming that your configuration changes are effective and finally creating a hostname to reference the site in your preferred web browser.

## Configuration
PHP and Apache will likely need to be further configured, especially to load the latest version of mod_php (the PHP module for Apache), to change any necessary runtime settings for both Apache and PHP, and especially to configure the Apache document root for this application.

Once that's done we still need to configure the appserver itself.  Check config/ in this repo for the relevant settings and examples.

## Installation directory
Usually something like c:\wamp64\www\appserver.


Install the Composer package manager:
--> sudo apt install composer

Step #1 - Clone this repository into the installation directory.

Step #2 - Composer - install the dependencies.
 --> cd appserver
 --> composer update
 
Resolve dependencies
 --> For example, if php-curl is not installed, run:
    sudo apt-get install php7.2-curl

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
 --> Test one of the provided routes.





# INSTRUCTIONS
## Using HTTP
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
