<?php
require "includes/HTTPRequest.php";
require "includes/HTTPResponse.php";

// 1. Open JSON file
// 2. Convert JSON to PHP
// 3. Foreach loop through list of websites
// 4. Instantiate an HTTPRequest object for each website
// 5. Get the status code of each website and save it
$fileName = "sites.json";
$handle = fopen ($fileName, "r");
$json = fread($handle,filesize($fileName));

$allSitesHealthy = true;

$sites = json_decode($json);
?>

<html>
    <head>
        <title>OCDLA Site Tester</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>

    <body>
        <div class="container">
            <h1 class="text-center m-2 mb-5">OCDLA Webpages:</h1>

            <div class="row m-2 mb-4">
                    <div class="col font-weight-bold">
                        <h5>Domain Name:</h5>
                    </div>
                    <div class="col font-weight-bold">
                        <h5>Status Code:</h5>
                    </div>
                    <div class="col font-weight-bold text-center">
                        <h5>Condition:</h5>
                    </div>
            </div>

            <?php foreach ($sites->sites as $site): ?>
                <?php //$newSite = new HTTPRequest("http://www.google.com"); ?>
                <div class="row m-2">
                    <div class="col">
                        <?php echo "$site->domain" ?>
                    </div>
                    <div class="col">
                        <?php echo "$site->status" ?>
                    </div>
                    <?php if($site->status == 200) { ?>
                        <div class="col text-center"> 
                            <a role="button" class="btn btn-success" href="<?php echo $site->domain ?>">Online</a>
                        </div>
                    <?php } else { 
                        $allSitesHealthy = false;    
                    ?>
                        <div class="col text-center"> 
                            <a role="button" class="btn btn-danger" href="<?php echo $site->domain ?>">Offline</a>
                        </div>
                    <?php } ?>
                </div>
            <?php endforeach; ?>

            <div class="row m-2 mt-4">
                <div class="col-8">
                    <h2>Overall Status:</h2>
                </div>
                <div class="col-4 text-center">
                    <?php if($allSitesHealthy) { ?>
                        <div class="btn btn-success">
                            Healthy
                        </div>
                    <?php } else { ?>
                        <div class="btn btn-danger">
                            Unhealthy
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>
