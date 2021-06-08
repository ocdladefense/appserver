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

		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=yes" />
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
		<link rel="icons" type="image/png" sizes="192x192"  href="/pwa-icons-192.png" />
		<link rel="apple-touch-startup-image" href="/apple-launch-750x1334.png" />
		<meta name="theme-color" content="#c4a12e" />
		<link type="text/css" href="/pwa-install.css" rel="stylesheet" />
		
		<script src="https://kit.fontawesome.com/c2278a45b9.js" crossorigin="anonymous"></script>

		<script src="/pwa-homescreen.js">
		</script>
		<title>OCDLA - Legal Tools & Research</title>

		<?php echo $styles; ?>

		
		<!-- link to the SqPaymentForm library -->


		
		<script type="text/javascript">
			window.appModules = {};
			
			
			function define(name,mod){
				if(typeof name == "string")
					appModules[name] = mod();
				else mod();
			}
			
			function getModule(name){
				if(!appModules[name]) throw new Error("Module "+name+" hasn't been loaded.");
				return appModules[name];
			}
			
		</script>




		<?php echo $scripts; ?> 
	</head>
	<body>

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
				<div id="logo">
					<a href="/home">
						<img src="/content/images/logo.png" />
					</a>
				</div>
				<div id="banner">&nbsp;</div>
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
						
				<li class="home-menu-item"><i class="fas fa-home fa-2x" aria-hidden="true"></i><a href="/home">home</a></li>

				<li class="home-menu-item"><i class="fas fa-people-arrows fa-2x"></i><a href="/covid" title="How OCDLA is pivoting to meet COVID-19 challenges.">covid-19</a></li>
			
				<li class="home-menu-item"><i class="fas fa-scroll"></i><a href="/motions" title="OCDLA motion bank">motion bank</a></li>
			
				<li class="home-menu-item"><i class="fas fa-book-open"></i><a href="/books" title="Read your publications">my publications</a></li>
			
				<li class="home-menu-item"><i class="fas fa-calendar-day fa-2x" aria-hidden="true"></i><a href="/events">events</a></li>
			
				<li class="home-menu-item"><i class="fas fa-briefcase" aria-hidden="true"></i><a href="/jobs">jobs</a></li>
			
				<li class="home-menu-item"><i class="fas fa-database fa-2x" aria-hidden="true"></i><a href="/car/list">case reviews</a></li>
			
				<li class="home-menu-item"><i class="fas fa-map-pin fa-2x" aria-hidden="true"></i><a href="/maps">maps</a></li>
			
				<li class="home-menu-item"><i class="fas fa-video fa-2x" aria-hidden="true"></i><a href="/videos">videos</a></li>
			
				<li class="home-menu-item"><i class="fas fa-user-friends fa-2x" aria-hidden="true"></i><a href="/directory">members/experts</a></li>
			
				<li class="home-menu-item"><i class="fas fa-comment-dots fa-2x" aria-hidden="true"></i><a href="/feedback">feedback</a></li>
			
				<li class="home-menu-item"><i class="fas fa-mobile-alt fa-2x" aria-hidden="true"></i><a href="/contact">contact us</a></li>
							
							</ul>
						</div>
					</div>
				
				
				
					<div id="stage" height="800px;">

						<div id="stage-content" style="height:1000px;">
								<i class="fas fa-home fa-2x"></i>
								<?php echo $content; ?>
							
						</div>
					
					</div>

					<!--
					<div id="container-right" class="column column-right">
						&nbsp;
					</div>
					-->
				
				</div> <!-- end container-content -->
				
			</div> <!--end container-->
						
			<div id="footer">
				<div class="content">
					<span style="font-size:smaller;">&copy;&nbsp;2020 OCDLA</span>
				</div> <!--end content-->
			</div> <!--end footer-->
			
		</div> <!--end page -->


	</body>

</html>
