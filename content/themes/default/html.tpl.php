<?php

use function Session\get_current_user;

// This is the template for the default theme which happens to be for OCDLA.....


 $user = get_current_user();
 $loginMessage = !$user->is_logged_in() ? "Logged in as Guest" : "Hello {$user->getFirstName()}";
 $profileUrl = !$user->is_logged_in() ? "#" : "/user/profile";

?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8" />

    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=yes" />
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />

    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-title" content="OCDLA" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="format-detection" content="telephone=no" />

    <link rel="apple-touch-icon" sizes="180x180" href="/pwa-icons-180.png" type="image/png" />
    <link rel="manifest" href="/manifest.json" />
    <link rel="icons" type="image/png" sizes="192x192" href="/pwa-icons-192.png" />
    <link rel="apple-touch-startup-image" href="/apple-launch-750x1334.png" />
    <meta name="theme-color" content="#c4a12e" />
    <link type="text/css" href="/pwa-install.css" rel="stylesheet" />

    <script src="https://kit.fontawesome.com/c2278a45b9.js" crossorigin="anonymous"></script>
    <script src="/pwa-homescreen.js"></script>



    <title>OCDLA - Legal Tools & Research</title>

    <?php echo $styles; ?>

    <?php echo $scripts; ?>

</head>

<body style="background-color: rgba(191,161,46,1.0); color: rgba(255,255,255,1.0);">

    <div style="min-height: 100%; min-width: 100%; background-color: #fff;">

        <div id="header">

            <div id="header-content" class="header-content">

				<div id="mobile-menu-btn"></div>

                <div id="logo">
                    <a href="//www.ocdla.org">
                        <img src="/content/images/logo.png" />
                    </a>
                </div>

                <div id="banner">&nbsp;</div>

                <div id="float-right">

                    <div id="search-form">

                        <form class="devsite-search-form" action="https://developers.google.com/s/results" method="GET">

                            <div class="devsite-search-container">
                                <div class="devsite-searchbox">
                                    <input style="padding:4px;" aria-activedescendant="" aria-autocomplete="list"
                                        aria-label="Search" aria-haspopup="false" aria-multiline="false"
                                        autocomplete="off" class="devsite-search-field devsite-search-query" name="q"
                                        placeholder="Search" type="text" value=""
                                        aria-controls="devsite-search-popout-container-id-1">
                                    <div class="devsite-search-image material-icons" aria-hidden="true"></div>
                                </div>
                            </div>

                            <div class="devsite-popout" id="devsite-search-popout-container-id-1">
                                <div class="devsite-popout-result devsite-suggest-results-container" devsite-hide=""></div>
                            </div>

                        </form>

                    </div>

                    <div id="user-area">

                        <?php if($user->is_logged_in()) : ?>
                            <a class="login" href="/logout">logout</a>
                        <?php else : ?>
                            <a class="login" href="/login">login</a>
                        <?php endif; ?>

                        <a id="user-icon" href="<?php print $profileUrl; ?>" title="<?php print $loginMessage; ?>">
                            <svg id="user-widget" width="40" height="40" viewBox="0 0 100 100"
                                xmlns="http://www.w3.org/2000/svg">
                                <g>
                                    <circle cx="50" cy="50" r="50" style="fill: rgba(210, 165, 80);" />
                                    <text x="50%" y="50%" font-size="3.0em" fill="#ffffff" text-anchor="middle"
                                        stroke="#ffffff" stroke-width="0px"
                                        dy=".3em"><?php print $user->getInitials(); ?></text>
                                </g>
                            </svg>
                        </a>

                    </div><!-- end user-area -->

                </div><!-- end float-right -->


                <!-- <div class="icon" id="mobile-icon">&#9776;</div> -->
            </div>

        </div>

        <!--end header-->




        <div id="page">

            <div id="container">

                <div class="container-content">

                    <div id="container-left" class="column column-left"></div>

                    <?php load_template("sidebar"); ?>


                    <div id="stage" class="column column-middle">

                        <div id="stage-content">

                            <?php echo $content; ?>

                        </div>

                    </div>

                </div> <!-- end container-content -->

            </div>
            <!--end container-->

            <div id="footer">

                <div class="content">
                    <span style="font-size:smaller;">&copy;&nbsp;2020 OCDLA</span>
                </div><!--end content-->

            </div> <!--end footer-->

        </div> <!--end page -->


        <div id="modal-backdrop">
            <div id="modal">
                <div id="modal-content"></div>
            </div>
        </div>

        <div id="loading">
            <div id="loading-wheel"></div>
        </div>

        <div id="positioned-context-container"></div>

    </div>


    <footer>
        <div class="container">
            &copy;&nbsp;Oregon Criminal Defense Lawyers Associaton 2020-2021
        </div>
    </footer>

</body>

</html>