<?php
namespace Marks\Acl;

use Illuminate\Routing\Router,
	Marks\Acl\Models\Aco as Aco;

class ControllerScanner{

	//scan routes
	public static function scan($exceptControllers = array(), $exceptActions = array()){
		array_push($exceptControllers, "Closure");
		array_push($exceptActions, "missingMethod");
		$routes = \Route::getRoutes();
		$list = array();
		foreach ($routes as $route) {
		   $routeArray = explode("@",$route->getActionName());
		   $controller = $routeArray[0];
		   if(in_array($controller, $exceptControllers)){}else{
			   $action = end($routeArray);
			   if(in_array($action, $exceptActions)){}else{
				   if(array_key_exists($controller, $list)){}else{$list[$controller] = array();}
				   if(in_array($action, $list[$controller])){}else{
				   		array_push($list[$controller], $action);
				   		Aco::firstOrCreate(array("controller"=>$controller, "action"=>$action));
				   		
				   }
			   }
		   }
		}
		return $list;
	}
}