<?php
/**
 * menu.inc
 * list some default menu router items
 *
 */
use Clickpdx\Core\Routing\Route;
 
define('MENU_ITEM_NORMAL',0x001);
define('MENU_ITEM_ROUTER',0x002);
define('MENU_GRANT_ACCESS',true);
define('MENU_DENY_ACCESS',true);

use Clickpdx\Core\Routing\RouteProcessor;

/**
 * Return all menu router items from all installed modules.
 * The keys of this array correspond to menu paths.
 */ 
function drupal_get_menu_items()
{
	global $menu_items;
	if(!isset($menu_items)) $menu_items = array();
	// Mimic enabled Drupal modules
	// instead store them statically in an array
	$groups = array(
		'site' 	=> _read_modules(drupal_get_active_site_dir().'/modules', false),
		'all' 	=> _read_modules(DRUPAL_ROOT.'/sites/all/modules', false)
	);
	
	foreach($groups as $group=>$modules)
	{
		foreach($modules as $name=>$configFile)
		{
			$func = $name . "_menu";
			if(function_exists($func)) $items = $func();
			else continue;
			if(!count($items)) continue; // BUGFIX #2016-001: empty arrays clobber in array_merge.
			array_walk($items,function(&$item) use($configFile,$name){
				$item['module']=$name;
				$item['module_path']=dirname($configFile);
			});
			$menu_items = array_merge($menu_items,$items);
		}
	}
	return $menu_items;
}


function menu_item_add($menuArgs)
{
	global $menu_items;
	if(!isset($menu_items)) $menu_items = array();
	$item = each($menuArgs);
	$route = new Route($item['key']);
	$route->setCallback($item['value']);
	// print entity_toString($menuArgs);exit;
	$menu_items = array_merge($menu_items,$route->__toMenuStorageItem());
}


/**
 * @function clickpdx_menu_parse_router_path
 * @description - prepare a router path, especially wildcard characters,
 *	to be used in regular expressions
 * @return - a Regular Expression
 *
 **/
function clickpdx_menu_regex_router_path($router_path)
{
	$patterns = array();
	$replacements = array();
	
	$patterns[0] = '/\//mis';
	$patterns[1] = '/%\w+/mis';
	
	$replacements[0] = '\/';
	$replacements[1] = '([\w\-\s\%\'\"+_\.]+)';

	
	$url_pattern = "/^".preg_replace( $patterns, $replacements, $router_path ) . "$/mis";

	return $url_pattern;
}



function clickpdx_get_router($requestedPath)
{
	if(empty($requestedPath)) return new Clickpdx\Core\Routing\Route();
	global $menu_items;
	
	$found = false;
	
	// Pop items off the end of the $current_path 
	$routes = array_keys($menu_items);
	$all_patterns = array();
	$possibles = array();
	
	// Locate the correct router item
	// convert each router path to a regular expression
	// and test it against the current path	
	foreach($routes AS $route)
	{
		$url_pattern = clickpdx_menu_regex_router_path($route);
		$all_patterns[] = $url_pattern;
		if(preg_match($url_pattern, $requestedPath, $matches))
		{
			$found = true;
			$possibles[] = $route;
			break;
		}
	}
	$router = new Clickpdx\Core\Routing\Route(
		$route,
		$requestedPath,
		$menu_items[$route]);
	return $router;
}


function clickpdx_path_arguments($path)
{
	return explode('/',$path);
}


function clickpdx_get_homepage_router()
{
	$menu_items = drupal_get_menu_items();	
	$path = system_get_homepage_callback();
	$route = new Clickpdx\Core\Routing\Route($path,$menu_items[$path]);
	return $route;
}



function getMenuRouterItem($key)
{
	global $menu_items;
	if(isset($menu_items[$key]))
	{
		return new Clickpdx\Core\Routing\Route(
			$key,
			$current_path,
			$menu_items[$key]
		);
	}
	else return false;
}


function getNextMenuItemParentKey($key)
{
	$parts = getPathParts($key);
	array_pop($parts);
	return (count($parts)?implode('/',$parts):false);
}


function evaluateMenuAccess($route,$path)
{
	if(is_bool($route->getAccessCallback())) return $route->getAccessCallback();
	if(function_exists($route->getAccessCallback()))
	{ 
		return $route->hasAccessArguments() ?
			call_user_func_array($route->getAccessCallback(),
				$route->getAccessArguments()) :
			call_user_func($route->getAccessCallback());
	}
	$parentKey = getNextMenuItemParentKey($route->getPath());
	$nextRouter = getMenuRouterItem($parentKey);
	if(!$nextRouter)
	{
		return MENU_GRANT_ACCESS;
	}
	return evaluateMenuAccess($nextRouter,$path);
}


function getPathParts($path)
{
	return explode('/',$path);
}


function menu_access_args($args,$arg)
{
	if(!is_array($args))
	{
		$args = array();
	}
	array_unshift($args,$arg);
	return $args;
}


function menu_get_menu( $name )
{
	$menus = array();
	$items = drupal_get_menu_items();
	while( $it = each( $items ) )
	{
		$menus[$it['value']['menu_name']][$it['key']] = $it['value']['title'];
	}
	return $menus[$name];
}

function menu_get_menus() {
	$menus = array();
	$items = drupal_get_menu_items();
	while( $it = each( $items ) ) {
		//tail( $it );
		$menus[$it['value']['menu_name']][$it['key']] = $it['value']['title'];
	}
	tail( $menus );
	return $menus;
}


function menu_submenu($moduleName)
{
	$menuFunc="{$moduleName}_menu";
	$items = $menuFunc();
	
	$m=function($path,$item)
	{	
		return ($item['type']==MENU_ITEM_ROUTER?null:"<a href='/{$path}'>{$item['title']}</a>");
	};
	$submenu = "<div class='submenu {$moduleName}-submenu'>".
		implode('&nbsp;|&nbsp;',array_filter(array_map($m,array_keys($items),$items))).
	"</div>";
	return $submenu;
}