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
<<<<<<< HEAD
		<link rel="manifest" href="/webconsole/manifest/manifest.json" />
		<meta name="viewport", content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=yes"/>
		<meta http-equiv="X-UA-Compatible", content="IE=Edge" />
=======
		<link rel="manifest" href="/manifest.webmanifest">
		<link rel="icons" type="image/png" sizes="169x169"  href="/images/icon.png">
>>>>>>> 8ae2f6e999741cf21f44b60bafbbab42b8a7502a
		<title>AppServer Client</title>

		<?php echo $styles; ?>
		
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

		<div id="page">
			<div id="header">
				<div id="header-content" class="header-content">
					<div id="logo">AppServer GUI</div>
					<div id="banner">&nbsp;</div>
					<div class="icon" id="mobile-icon">&#9776;</div>
				</div>
			</div> <!--end header-->
			
			<div id="doc-header">
				<div id="doc-nav">

					<div class="doc-nav" id="doc-nav-left">
					 Some content here
					</div>
				
					<div class="doc-nav" id="doc-nav-content">
						<span id="doc-nav-open" data-controller-for="doc-nav" class="collapsible-controller">
							<span class="arrow-right">&nbsp;</span>
						</span>

					</div>

					<div class="doc-nav" id="doc-nav-right">
						Some content here
					</div>
				
				</div>
			</div>
			
			<div id="container">
				<div id="container-left">
					<div id="menu-left">&nbsp;</div>
				</div>
					
				<div id="stage" class="<?php echo $defaultStageClass; ?>">

					<div id="stage-content">
						<author>Steve Sherlag</author>
						<doc>
							<title>Chapter 4</title>
							<author>Steve Eberlein</author>
							<author>Seth White</author>
						</doc>
						<?php /*echo $content;*/ ?></div>
				</div>

				<div id="container-right" class="container-right"></div>
			</div> <!--end container-->
						
			<div id="footer">
				<div class="content">
					<span style="font-size:smaller;">&copy;&nbsp;2019</span>
				</div> <!--end content-->
			</div> <!--end footer-->
			
		</div> <!--end page -->

		
		<div id="modal-backdrop">
			<div id="modal">
				<div id="modal-content"></div>
			</div>
		</div>
		
		<div id="context-menu-container">
			<div id="context-menu" style="height:200px; width:90%; background-color:teal;"></div>
		</div>
		
		<div id="positioned-context-container"></div>

	
		<script type="text/javascript">
	
			// domReady(renderMenu);
			var app;
		
			domReady(function(){
				app = new App();
				// app.addRoutes(clientSettings["routes-enabled"]);
				app.init(clientSettings);
				// app.setKeyboardManager(kbd);
			});
	
			function renderMenu() {
				var vnode = createMenu();
				var node = createElement( vnode );
				document.getElementById("header").appendChild(node);
			}
	
			renderMenu();

		</script>
		
		<!--<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>-->
		<!-- <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		-->
		<!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

		-->

	</body>

</html>
