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
		<title>Using modal cc form</title>
		<link href="/examples/assets/css/ux.css" type="text/css" rel="stylesheet" />
		<link href="/modules/webconsole/assets/css/materials.css" type="text/css" rel="stylesheet" />
		<link href="/modules/webconsole/assets/css/keyboardManager.css" type="text/css" rel="stylesheet" />
		<script type="text/javascript">
			window.appModules = {};
			function define(deps,mod){
				mod();
			}

		</script>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="//membertest.ocdla.org/sites/all/libraries/library/event.js">
		</script>
		<script src="//membertest.ocdla.org/sites/all/libraries/library/view-core.js">
		</script>
		<script src="/examples/assets/js/menu.js">
		</script>
		<script src="/modules/webconsole/assets/keyboardManager.js">
		</script>
		<script src=/modules/webconsole/assets/user-routes.js>
		</script>
		<script src=/modules/webconsole/assets/viewLibrary.js>
		</script>
		<script src=/modules/webconsole/assets/components/materials.js>
		</script>
		<script src=/modules/webconsole/assets/fetch.js>
		</script>
		<script src=/modules/webconsole/assets/app.js>
		</script>

    	 <?php /* echo $scripts*/ ?> 
	</head>
	<body>
	
   
    </div>
		<div id="page">
            
			<div class="menu-left">&nbsp;</div>
			
			<div id="stage"><h1><?php echo $content  ?></h1></div>

			<div id="header">
			<div id="logo">AppServer GUI</div>
			<div id="banner">&nbsp;
			</div>
		</div>
		<h1>View or download event materials below</h1>
    	<div class="container2" id = "dropDownContainer">
        <select id = "chapterDropDown">
        <option selected="selected" value = "Show All Chapters">Show All Chapters</option>
        </select>

        <button>Download .zip of all event materials</button>
    </div>

    <hr>
    <div class = "table-container2", id="table-container2">

	</div>
		
		
		<div id="footer">
		 	<div class="content">
		 		<span style="font-size:smaller;">&copy;&nbsp;2019</span>
		 	</div>
		</div>
	</body>
	
	<script type="text/javascript">
	
    // domReady(renderMenu);
    
    
	
	function renderMenu() {
		var vnode = createMenu();
		var node = createElement( vnode );
		document.getElementById("header").appendChild(node);
	}
	
	renderMenu();

	</script>
</html>
