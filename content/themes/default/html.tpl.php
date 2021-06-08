<?php
/**
 * This is the webconsole template file.
 * Reference it like so: 
 *   $template = new Template("webconsole");
 */
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
    <!--
		<link rel="apple-touch-icon" sizes="152x152" href="/apple-touch-icon-ipad.png" type="image/png" />
		<link rel="apple-touch-icon" sizes="167x167" href="/apple-touch-icon-ipad-retina.png" type="image/png" />
		-->
    <link rel="apple-touch-icon" sizes="180x180" href="/pwa-icons-180.png" type="image/png" />
    <link rel="manifest" href="/manifest.json" />
    <link rel="icons" type="image/png" sizes="192x192" href="/pwa-icons-192.png" />
    <link rel="apple-touch-startup-image" href="/apple-launch-750x1334.png" />
    <meta name="theme-color" content="#c4a12e" />
    <link type="text/css" href="/pwa-install.css" rel="stylesheet" />

    <style type="text/css">
    .fullscreen #container-left {
        display: none;
    }

    .fullscreen #stage {
        width: 100% !important;
    }

    .fullscreen #stage-content {
        height: 100vh;
    }

    #header {
        transition: height 0.3s ease-in-out 0.5s;
        overflow: hidden;
    }

    .fullscreen #header {
        height: 0px;
    }

    #container {
        transition: padding-top 0.3s ease-out 1.0s;
    }

    .fullscreen #container {
        padding-top: 0px;
    }

    .doc {
        display: inline-block;
        text-align: center;
        width: 45%;
        vertical-align: top;
        margin-bottom: 40px;
    }

    .doc-title {

        padding-top: 4px;
        font-family: "Roboto", sans-serif;
        /* text-transform: uppercase; */
        font-size: 16.5px;
        font-weight: 600;
    }

    .doc-chapters {}

    .doc-published {}

    .doc span {
        display: block;
    }
    </style>

    <script src="https://kit.fontawesome.com/c2278a45b9.js" crossorigin="anonymous"></script>

    <script src="/pwa-homescreen.js">
    </script>
    <title>OCDLA - Legal Tools & Research</title>

    <?php echo $styles; ?>


    <!-- link to the SqPaymentForm library -->



    <script type="text/javascript">
    window.appModules = {};

    function triggerEvent(name, data) {

        let event = new CustomEvent(name, {
            detail: data
        });
        document.dispatchEvent(event);
    }


    /*
    function define(name,mod){
    	if(typeof name == "string")
    		appModules[name] = mod();
    	else mod();
    }
    
    function getModule(name){
    	if(!appModules[name]) throw new Error("Module "+name+" hasn't been loaded.");
    	return appModules[name];
    }
    */
    </script>




    <?php echo $scripts; ?>
</head>

<body style="background-color: rgba(191,161,46,1.0); color: rgba(255,255,255,1.0);">
    <div style="min-height: 100%; min-width: 100%; background-color: #fff;">
        <!--
		<div id="babel-foo" style="position:absolute;top:0px;right:0px;background-color:#fff;z-index:101;max-width:325px;padding:10px;">&nbsp;</div>
		<script type="text/babel">

			ReactDOM.render(
				<div><h1>Hello, JSX!</h1><p>You can find this additional JSX component in <em>webconsole.tpl.php</em>.  Comment me out once it's working in the external file.</p></div>,
				document.getElementById('babel-foo')
			);

		</script>
		-->


        <div id="header">

            <div id="header-content" class="header-content">
				<div id="mobile-menu-btn"></div>
                <div id="logo">
                    <a href="//www.ocdla.org">
                        <img src="/content/images/logo.png" />
                    </a>
                </div>
                <div id="banner">&nbsp;</div>



                <!-- see https://developers.google.com/web/fundamentals/web-components/customelements -->
                <div id="float-right" style="display:inline-block; float:right;">


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
                                <!--<button type="button" search-open="" class="devsite-search-button devsite-header-icon-button button-flat material-icons" aria-label="Open search"></button>-->
                            </div>
                            <div class="devsite-popout" id="devsite-search-popout-container-id-1">
                                <div class="devsite-popout-result devsite-suggest-results-container" devsite-hide="">
                                </div>
                            </div>
                        </form>
                    </div>

                    <div id="user-menu" style="display:inline-block; margin-top:11px;">

                        <a href="/user/settings" title="Settings" style="display:none; margin-right: 8px;">
                            <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M14.31 20.03C14.21 20.71 13.59 21.25 12.85 21.25H9.14999C8.40999 21.25 7.78999 20.71 7.69999 19.98L7.42999 18.09C7.15999 17.95 6.89999 17.8 6.63999 17.63L4.83999 18.35C4.13999 18.61 3.36999 18.32 3.02999 17.7L1.19999 14.53C0.84999 13.87 0.99999 13.09 1.55999 12.65L3.08999 11.46C3.07999 11.31 3.06999 11.16 3.06999 11C3.06999 10.85 3.07999 10.69 3.08999 10.54L1.56999 9.35001C0.97999 8.90001 0.82999 8.09001 1.19999 7.47001L3.04999 4.28001C3.38999 3.66001 4.15999 3.38001 4.83999 3.65001L6.64999 4.38001C6.90999 4.21001 7.16999 4.06001 7.42999 3.92001L7.69999 2.01001C7.78999 1.31001 8.40999 0.76001 9.13999 0.76001H12.84C13.58 0.76001 14.2 1.30001 14.29 2.03001L14.56 3.92001C14.83 4.06001 15.09 4.21001 15.35 4.38001L17.15 3.66001C17.86 3.40001 18.63 3.69001 18.97 4.31001L20.81 7.49001C21.17 8.15001 21.01 8.93001 20.45 9.37001L18.93 10.56C18.94 10.71 18.95 10.86 18.95 11.02C18.95 11.18 18.94 11.33 18.93 11.48L20.45 12.67C21.01 13.12 21.17 13.9 20.82 14.53L18.96 17.75C18.62 18.37 17.85 18.65 17.16 18.38L15.36 17.66C15.1 17.83 14.84 17.98 14.58 18.12L14.31 20.03ZM9.61999 19.25H12.38L12.75 16.7L13.28 16.48C13.72 16.3 14.16 16.04 14.62 15.7L15.07 15.36L17.45 16.32L18.83 13.92L16.8 12.34L16.87 11.78L16.8731 11.7531C16.902 11.5027 16.93 11.2607 16.93 11C16.93 10.73 16.9 10.47 16.87 10.22L16.8 9.66001L18.83 8.08001L17.44 5.68001L15.05 6.64001L14.6 6.29001C14.18 5.97001 13.73 5.71001 13.27 5.52001L12.75 5.30001L12.38 2.75001H9.61999L9.24999 5.30001L8.71999 5.51001C8.27999 5.70001 7.83999 5.95001 7.37999 6.30001L6.92999 6.63001L4.54999 5.68001L3.15999 8.07001L5.18999 9.65001L5.11999 10.21C5.08999 10.47 5.05999 10.74 5.05999 11C5.05999 11.26 5.07999 11.53 5.11999 11.78L5.18999 12.34L3.15999 13.92L4.53999 16.32L6.92999 15.36L7.37999 15.71C7.80999 16.04 8.23999 16.29 8.70999 16.48L9.23999 16.7L9.61999 19.25ZM14.5 11C14.5 12.933 12.933 14.5 11 14.5C9.06699 14.5 7.49999 12.933 7.49999 11C7.49999 9.06701 9.06699 7.50001 11 7.50001C12.933 7.50001 14.5 9.06701 14.5 11Z"
                                    fill="#5F6368"></path>
                            </svg>
                        </a>

                        <?php if(true || is_authenticated()): ?>
                        <a href="/user/profile" title="You're logged in using <?php print $_SESSION["username"]; ?>">
                            <?php else: ?>
                            <a href="/login" title="You're a guest - login.">
                                <?php endif; ?>
                                <svg id="user-widget" width="40" height="40" viewBox="0 0 100 100"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <g>
                                        <circle cx="50" cy="50" r="50" style="fill: rgba(210, 165, 80);" />
                                        <text x="50%" y="50%" font-size="3.0em" fill="#ffffff" text-anchor="middle"
                                            stroke="#ffffff" stroke-width="0px"
                                            dy=".3em"><?php print user_get_initials(); ?></text>
                                    </g>
                                </svg>
                            </a>

                    </div><!-- end user menu -->

                </div><!-- end float-right -->


                <!-- <div class="icon" id="mobile-icon">&#9776;</div> -->
            </div>

        </div>

        <!--end header-->




        <div id="page">

            <div id="container">

                <div class="container-content">

                    <!--
						<a href="/home">
							<img id="logo" src="/content/images/logo.png" alt="Back to app home." />
						</a>
					-->
                    <div id="container-left" class="column column-left">
                        <div id="menu-left">
                            <ul class="main-menu">

                                <li class="home-menu-item"><i class="fas fa-home fa-2x" aria-hidden="true"></i><a
                                        href="/home">home</a></li>

                                <li class="home-menu-item"><i class="fas fa-people-arrows fa-2x"></i><a href="/covid"
                                        title="How OCDLA is pivoting to meet COVID-19 challenges.">covid-19</a></li>

                                <li class="home-menu-item"><i class="fas fa-scroll"></i><a href="/documents"
                                        title="OCDLA motion bank">motion bank</a></li>

                                <li class="home-menu-item"><i class="fas fa-book-open"></i><a href="/user/documents"
                                        title="Read your publications">my publications</a></li>

                                <li class="home-menu-item"><i class="fas fa-calendar-day fa-2x"
                                        aria-hidden="true"></i><a href="/events">events</a></li>

                                <li class="home-menu-item"><i class="fas fa-briefcase" aria-hidden="true"></i><a
                                        href="/jobs">jobs</a></li>

                                <li class="home-menu-item"><i class="fas fa-database fa-2x" aria-hidden="true"></i><a
                                        href="/car/list">case reviews</a></li>

                                <li class="home-menu-item"><i class="fas fa-map-pin fa-2x" aria-hidden="true"></i><a
                                        href="/maps">maps</a></li>

                                <li class="home-menu-item"><i class="fas fa-video fa-2x" aria-hidden="true"></i><a
                                        href="/videos">videos</a></li>

                                <li class="home-menu-item"><i class="fas fa-user-friends fa-2x"
                                        aria-hidden="true"></i><a href="/directory">members/experts</a></li>

                                <li class="home-menu-item"><i class="fas fa-comment-dots fa-2x"
                                        aria-hidden="true"></i><a href="/feedback">feedback</a></li>

                                <li class="home-menu-item"><i class="fas fa-mobile-alt fa-2x" aria-hidden="true"></i><a
                                        href="/contact">contact us</a></li>

                            </ul>
                        </div>
                    </div>

                    <?php 
						load_template("sidebar");
						//include(BASE_PATH.DIRECTORY_SEPARATOR."content".DIRECTORY_SEPARATOR."themes".
							//DIRECTORY_SEPARATOR."default".DIRECTORY_SEPARATOR."sidebar.tpl.php"); 
					?>


                    <div id="stage" class="column column-middle">

                        <div id="stage-content">

                            <?php echo $content; ?>

                        </div>

                    </div>

                    <!--
					<div id="container-right" class="column column-right">
						&nbsp;
					</div>
					-->

                </div> <!-- end container-content -->

            </div>
            <!--end container-->

            <div id="footer">
                <div class="content">
                    <span style="font-size:smaller;">&copy;&nbsp;2020 OCDLA</span>
                </div>
                <!--end content-->
            </div>
            <!--end footer-->

        </div>
        <!--end page -->


        <div id="modal-backdrop">
            <div id="modal">
                <div id="modal-content"></div>
            </div>
        </div>

        <div id="loading">
            <div id="loading-wheel"></div>
        </div>

        <!--
		<div id="context-menu-container">
			<div id="context-menu" style="height:200px; width:90%; background-color:teal;"></div>
		</div>
		-->

        <div id="positioned-context-container"></div>

        <?php if(false !== $doInit): ?>
        <script type="text/javascript">
        // domReady(renderMenu);


        var app;

        /*
        domReady(function(){
        	app = new App();
        	// app.addRoutes(clientSettings["routes-enabled"]);
        	try {
        		app.init(clientSettings);
        	} catch(e) {
        		console.log(e);
        	}	
        	// app.setKeyboardManager(kbd);
        });
        */
        </script>
        <?php endif; ?>

        <script type="text/javascript">
        function fullscreen() {
            document.body.classList.add("fullscreen");
        }
        /*
			function renderMenu() {
				var vnode = createMenu();
				var node = createElement( vnode );
				document.getElementById("header").appendChild(node);
			}
	
			renderMenu();
			*/
        </script>

        <!--
		<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
		-->

        <!--
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		-->

        <!--
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
		-->

    </div>


    <footer>
        <div class="container">
            &copy;&nbsp;Oregon Criminal Defense Lawyers Associaton 2020-2021
        </div>
    </footer>
</body>

</html>