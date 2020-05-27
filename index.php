<?php

// phpinfo();


require "includes/Http/HttpRequest.php";
require "includes/Http/HttpResponse.php";

// this is an admin user
define("USER_LEVEL_ADMIN", 1);

// this is a normal user
define("USER_LEVEL_PUBLIC", 2);

// create a test public user and populate it
$publicUser = new stdClass();

$publicUser->firstName = "John";
$publicUser->lastName = "Doe";
$publicUser->accessLevel = USER_LEVEL_PUBLIC;

// create a test admin user and populate it
$adminUser = new stdClass();

$adminUser->firstName = "Jane";
$adminUser->lastName = "Doe";
$adminUser->accessLevel = USER_LEVEL_ADMIN;

define("USER_ACCESS_GRANTED", 1);
define("USER_ACCESS_DENIED", 0);

function accessDenied() {
    return USER_ACCESS_DENIED;
}

function accessGranted() {
    return USER_ACCESS_GRANTED;
}

function hasAccess($user) {
    return $user->accessLevel == USER_LEVEL_ADMIN;
}


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

//// test code for HTTPRequest
// foreach($sites->sites as $website) {
//     $test = new HTTPRequest($website->domain);
//     $newResponse = $test->makeHTTPRequest();
//     print_r($test->getStatus());
// }
?>

<html>
    <head>
        <title>OCDLA Site Tester</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>

    <body>
        <div class="container">
            <h1 class="text-center m-2 mb-5">OCDLA Webpages:</h1>

            <!-- Row of Headings -->
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
                    <?php if(hasAccess($adminUser)): ?>
                    <div class="col font-weight-bold text-center">
                        <h5>Additional Info:</h5>
                    </div>
                    <?php endif; ?>
            </div>

            <!-- Loop through every site in the sites.json and print a new row with all info for that site-->
            <?php foreach ($sites->sites as $site): ?>
                
                <!-- make a new request object for the site -->
                <?php $request = new HTTPRequest($site->domain);
                      $response = $request->makeHTTPRequest(); ?>
                
                
                <div class="row m-2">

                    <!-- print the domain of the site -->
                    <div class="col">
                        <strong><?php echo "$site->domain" ?></strong>
                    </div>

                    <!-- print the current status of the site -->
                    <div class="col">
                        <?php echo $request->getStatus() ?>
                    </div>

                    <!-- Create a "Site Status" button that is styled based on the status of the site -->
                    <?php if($site->expectedStatus == $request->getStatus()) : ?>
                        <div class="col text-center"> 
                            <a role="button" class="btn btn-success" href="<?php echo $site->domain ?>">Online</a>
                        </div>
                    <?php else: 
                        $allSitesHealthy = false;    
                    ?>
                        <div class="col text-center"> 
                            <a role="button" class="btn btn-danger" href="<?php echo $site->domain ?>">Offline</a>
                        </div>
                    <?php endif; ?>

                    <!-- If the user is an admin, create an info button that shows additional information -->
                    <?php if(hasAccess($adminUser)) : ?>
                        <div class="col text-center">
                            <a role="button" class="btn btn-primary" href="#<?php echo preg_replace('/\s/', '', $site->name) ?>" data-toggle="collapse" aria-expanded="false" aria-controls="<?php echo preg_replace('/\s/', '', $site->name) ?>">Info</a>
                        </div>
                        <div class="collapse container" id="<?php echo preg_replace('/\s/', '', $site->name) ?>">
                            <div class="card card-body m-3">
                                <div class="row m-2 pl-3">
                                    <strong>Remote Repository:&nbsp;</strong> <?php echo $site->repositoryUrl ?>
                                </div>

                                <!-- Row of headings -->
                                <div class="row m-2 mt-4">
                                    <div class="col">
                                        <h6>Path Name:</h6>
                                    </div>
                                    <div class="col">
                                        <h6>Expected Status Code:</h6>
                                    </div>
                                    <div class="col">
                                        <h6>Current Status Code:</h6>
                                    </div>
                                    <div class="col text-center">
                                        <h6>Path Condition:</h6>
                                    </div>
                                </div>

                                <!-- Loop through urls and make a new line for each one -->
                                <?php foreach ($site->urls as $url): ?>
                                    <div class="row m-2">
                                        <div class="col">
                                            <?php echo "$url->path" ?>
                                        </div>
                                        <div class="col">
                                            <?php echo "$url->expectedStatusCode" ?>
                                        </div>
                                        <div class="col">
                                            <?php echo "$url->actualStatusCode" ?>
                                        </div>
                                        <?php if($url->expectedStatusCode == $url->actualStatusCode) { ?>
                                            <div class="col text-center"> 
                                                <a role="button" class="btn btn-success" href="<?php echo $url->path ?>">Healthy</a>
                                            </div>
                                        <?php } else { ?>
                                            <div class="col text-center"> 
                                                <a role="button" class="btn btn-danger" href="<?php echo $url->path ?>">Critical</a>
                                            </div>
                                        <?php } ?>
                                    </div>    
                                <?php endforeach; ?>
                            </div> <!-- End of card -->
                        </div>
                    <?php endif; ?> <!-- End of admin info button -->
                </div>
            <?php endforeach; ?> <!-- End of site loop -->

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
