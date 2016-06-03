<?php
namespace Marks\Acl\Resources;

use Illuminate\Routing\Router,
	Marks\Acl\Models\Aco as Aco;

class ControllerScanner{

	//scan routes
	public static function scan(){
		$exceptControllers = \Config::get("acl.except_controllers", array());
		$exceptActions = \Config::get("acl.except_actions", array());
		array_push($exceptControllers, "Closure");
		array_push($exceptActions, "missingMethod");
		$routes = \Route::getRoutes();
		$list = array();
		foreach ($routes as $route) {
		   $action = $route->getAction();
		   $routeArray = explode("@",$route->getActionName());
		   preg_match('/(?:get|post)(.*)/', $routeArray[1], $routeMatch);
		   $routeArray[1] = end($routeMatch);
		   $routePrefix = $action['acl_prefix'] ?: $route->getPrefix();
		   $controller = $routeArray[0];
		   if(!in_array($controller, $exceptControllers)){
			   $action = end($routeArray);
			   if(!in_array($action, $exceptActions)){
				   if(!array_key_exists($controller, $list)){$list[$controller] = array();}
				   if(!in_array($action, $list[$controller])){
				   		array_push($list[$controller], $action);
				   		Aco::firstOrCreate(array("controller"=>$controller, "action"=>$action, "prefix"=>$routePrefix));
				   		
				   }
			   }
		   }
		}
		return $list;
	}
}