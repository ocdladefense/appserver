<?php



require_once BASE_PATH.'/includes/Interfaces/IQueryable.php';
require_once BASE_PATH.'/includes/Interfaces/ISessionHandler.php';

if(defined("COMPOSER_VENDOR_PATH")) {
	require COMPOSER_VENDOR_PATH.'/vendor/autoload.php';

} else if(file_exists(BASE_PATH.'/vendor/autoload.php')) {

	include BASE_PATH.'/vendor/autoload.php';
}




require_once BASE_PATH.'/includes/Input/Identifier.php';

require_once BASE_PATH.'/includes/globals/system.php';
require_once BASE_PATH.'/includes/globals/files.php';
require_once BASE_PATH.'/includes/globals/cache.php';
require_once BASE_PATH.'/includes/globals/user.inc';
require_once BASE_PATH.'/includes/globals/access.php';
require_once BASE_PATH.'/includes/globals/authorization.php';
require_once BASE_PATH.'/includes/globals/api.php';
require_once BASE_PATH.'/includes/globals/theme.inc';


// require_once BASE_PATH.'/includes/User/User.php';
require_once BASE_PATH.'/includes/User/SalesforceUser.php';


require_once BASE_PATH.'/includes/Presentation/Component.php';
require_once BASE_PATH.'/includes/Presentation/Theme.php';	
require_once BASE_PATH.'/includes/Presentation/Template.php';	
require_once BASE_PATH.'/includes/Presentation/IRenderable.php';





require_once(BASE_PATH.'/includes/Module/Module.php');

$systemFiles = array("ClassName","XList", "IJson", "DateRange", "CoreModule","Session","Cache");

foreach($systemFiles as $file) {
	require_once(BASE_PATH.'/includes/System/'.$file.".php");
}



// Require all of the files in the includes/File directory.
$files = array("FileSystem","File", "FileList", "FileHandler", "PhpFileUpload");

foreach($files as $file) {
	require_once(BASE_PATH.'/includes/File/'.$file.".php");
}



require_once BASE_PATH.'/includes/Url/Url.php';
require_once BASE_PATH.'/includes/Url/RelativeUrl.php';
require_once BASE_PATH.'/includes/Url/AbsoluteUrl.php';
require_once BASE_PATH.'/includes/Url/QueryString.php';



$store = array("IPaymentProcessor","Customer","Order","CreditCard","Payment");

foreach($store as $file) {
	require_once(BASE_PATH.'/includes/Store/'.$file.".php");
}


$handlers = array("Handler","StringHandler","HtmlEmailHandler","ApplicationFileHandler","HttpResponseHandler","JsonHandler","HtmlDocumentHandler","JsonErrorHandler","HtmlStringHandler","TemplateHandler","ArrayHandler","ErrorHandler", "XmlHandler");


foreach($handlers as $file) {
	require_once(BASE_PATH.'/includes/Handlers/'.$file.".php");
}

	
require_once BASE_PATH.'/includes/Exception/PageNotFoundException.php';
require_once BASE_PATH.'/includes/Exception/SalesforceAccessException.php';
require_once BASE_PATH.'/includes/Exception/SalesforceAuthException.php';



require_once BASE_PATH.'/includes/Internationalization/Translate.php';

require_once BASE_PATH.'/includes/Routing/Path.php';	
require_once BASE_PATH.'/includes/Routing/Route.php';
require_once BASE_PATH.'/includes/Routing/Router.php';

require_once BASE_PATH.'/includes/Module/Module.php';

require_once BASE_PATH.'/includes/System/CoreModule.php';

require_once BASE_PATH. "/includes/Mail/MailMessage.php";
require_once BASE_PATH. "/includes/Mail/MailMessageList.php";
require_once BASE_PATH. "/includes/Mail/MailClient.php";
require_once BASE_PATH. "/includes/Mail/MailClientSes.php";



require_once BASE_PATH.'/includes/Application.php';


// To be modules
require_once BASE_PATH.'/includes/Store/Product.php';
require_once BASE_PATH.'/includes/Store/Salesforce/ShoppingCart.php'; 
require_once BASE_PATH.'/includes/Store/Salesforce/PaymentProcessor.php'; 

require_once BASE_PATH.'/includes/Store/Square/ShoppingCart.php';
require_once BASE_PATH.'/includes/Store/Square/PaymentProcessor.php'; 




// end of file.
