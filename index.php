<?php
require "appserver/includes/HTTPRequest.php";
require "appserver/includes/HTTPResponse.php";

// 1. Open JSON file
// 2. Convert JSON to PHP
// 3. Foreach loop through list of websites
// 4. Instantiate an HTTPRequest object for each website
// 5. Get the status code of each website and save it
$fileName = "sites.json";
$handle = fopen ($fileName, "r");
$json = fread($handle,filesize($fileName));


$sites = json_decode($json);
?>
<html>
<head></head>

<body>
<?php
print_r($sites);

foreach ($sites->sites as $site) {
    echo "<h1>{$site->domain}</h1>";
}
?>
</body>
</html>
