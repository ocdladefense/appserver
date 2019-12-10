<?php

//// test code for HTTPRequest
// foreach($sites->sites as $website) {
//     $test = new HTTPRequest($website->domain);
//     $newResponse = $test->makeHTTPRequest();
//     print_r($test->getStatus());
// }
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8" />
		<title>AppServer Client</title>
		<link href="/modules/webconsole/assets/css/ux.css" type="text/css" rel="stylesheet" />
		<link href="/modules/webconsole/assets/css/materials.css" type="text/css" rel="stylesheet" />
		<link href="/modules/webconsole/assets/css/KeyboardManager.css" type="text/css" rel="stylesheet" />
		<link href="/modules/webconsole/assets/modules/note/styles.css" type="text/css" rel="stylesheet" />
		<script type="text/javascript">
			window.appModules = {};
			
			function define(name,mod){
				if(typeof name == "string")
					appModules[name] = mod();
				else mod();
			}
			
			function getModule(name){
				return appModules[name];
			}
		</script>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js">
		</script>
		<script src="//membertest.ocdla.org/sites/all/libraries/library/event.js">
		</script>
		<script src="//membertest.ocdla.org/sites/all/libraries/library/view-core.js">
		</script>


    	 <?php echo $scripts; ?> 
	</head>
	<body>

		<div id="page">
			<div id="header">
				<div id="logo">AppServer GUI</div>
				<div id="banner">&nbsp;</div>
			</div> <!--end header-->
			
			<div id="container">
				<div id="menu-left">
					<ul id="sex-cases-menu">
						<li>foobar</li>
						<li>baz</li>
						<li>pow</li>
					</ul>
					

				</div>
					
				<div id="stage">

					<div id="stage-content"><?php echo $content; ?></div>
					
					<div class="has-context" style="height:200px; width:40%; background-color:blue;"></div>

				</div>
			</div> <!--end container-->
						
			<div id="footer">
				<div class="content">
					<span style="font-size:smaller;">&copy;&nbsp;2019</span>
				</div> <!--end content-->
			</div> <!--end footer-->
		</div> <!--end page -->

		</div>
		
		<div id="modal-backdrop">
			<div id="modal">
				<div id="modal-content"></div>
			</div>
		</div>
		
		<div id="context-menu-container">
			<div id="context-menu" style="height:200px; width:90%; background-color:teal;"></div>
		</div>
	</body>
	
	<script type="text/javascript">
	
    // domReady(renderMenu);
    var app;
    
		jQuery(function(){
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
</html>
