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
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js">
		</script>
		
		<?php echo $scripts; ?> 
	</head>
	<body>

		<div id="page">
			<div id="header">
				<div class="header-content">
					<div id="logo">AppServer GUI</div>
					<div id="banner">&nbsp;</div>
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

				<div id="container-right" class="container-right">

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
		<div id="positioned-context-container">
		
		</div>

	
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
