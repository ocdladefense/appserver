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
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
		<link href="/modules/webconsole/assets/css/ux.css" type="text/css" rel="stylesheet" />
		<link href="/modules/webconsole/assets/css/materials.css" type="text/css" rel="stylesheet" />
		<link href="/modules/webconsole/assets/css/keyboardManager.css" type="text/css" rel="stylesheet" />
		<link href="/modules/webconsole/assets/css/siteStatus.css" type="text/css" rel="stylesheet" />
		<script type="text/javascript">
			window.appModules = {};
			function define(deps,mod){
				mod();
			}

		</script>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js">
		</script>
		<script src="/modules/webconsole/assets/components/materials.js">
		</script>
		<script src="//membertest.ocdla.org/sites/all/libraries/library/event.js">
		</script>
		<script src="//membertest.ocdla.org/sites/all/libraries/library/view-core.js">
		</script>
		<script src="/modules/webconsole/assets/menu.js">
		</script>
		<script src="/modules/webconsole/assets/keyboardManager.js">
		</script>
		<script src="/modules/webconsole/assets/modules/editable/editable.js">
		</script>
		<script src="/modules/webconsole/assets/modules/note/note.js">
		</script>
		<script src="/modules/webconsole/assets/default-routes.js">
		</script>
		<script src="/modules/webconsole/assets/ui.js">
		</script>
		<script src="/modules/webconsole/assets/modal.js">
		</script>
		<script src="/modules/webconsole/assets/viewLibrary.js">
		</script>
		<script src="/modules/webconsole/assets/http.js">
		</script>
		<script src="/modules/webconsole/assets/settings.js">
		</script>
		<script src="/modules/webconsole/assets/app.js">
		</script>

    	 <?php /* echo $scripts*/ ?> 
	</head>
	<body>

		<div id="page">
			<div id="header">
				<div id="logo">AppServer GUI</div>
				<div id="banner">&nbsp;</div>
			</div> <!--end header-->
			
			<div id="container">
				<div id="menu-left">&nbsp;this is the menu-left</div>
					
				<div id="stage">
					<h1>Console loaded</h1>
					<div id="stage-content"><?php echo $content; ?></div>
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
	</body>
	
	<script type="text/javascript">
	
    // domReady(renderMenu);
    var app;
    
		jQuery(function(){
			app = new App();
			app.addRoutes(clientSettings["routes-enabled"]);
			app.init();
			// app.setKeyboardManager(kbd);
		});
	
		function renderMenu() {
			var vnode = createMenu();
			var node = createElement( vnode );
			document.getElementById("header").appendChild(node);
		}
	
		renderMenu();

	</script>
	<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

</html>
