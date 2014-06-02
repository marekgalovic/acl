<?php

/* Acl laravel package by Marek Galovic */

namespace Marks\Acl;

use Marks\Acl\ControllerScanner as Scanner,
	Marks\Acl\Models\Aco as Aco,
	Marks\Acl\Models\Aro as Aro;

class AclClass{
	
	private $route;
	private $controller;
	private $action;
	
	private $acoID = 0;
	private $aroID = 0;
	private $allowed = false;
	
	public function __construct(){
	
	}
	
	//identify user
	public function identify($id){
		$this->aroID = $id;
	}
	
	//check if user is allowed to perform action
	public function check(){
		$allowed = \DB::table("acl_aco_aro")->where("aro_id", "=", $this->aroID)->where("aco_id", "=", $this->acoID)->first();
		if(!$allowed){}
		else{
			$this->allowed = $allowed->allowed;
		}
		return $this->allowed;	
	}
	
	//return if is allowed
	public function allowed(){
		$this->loadRoute();
		return $this->check();	
	}
	
	private function loadRoute(){
		$this->route = explode("@",\Route::currentRouteAction());
		$this->controller = $this->route[0];
		$this->action = end($this->route);
		$this->acoID = Aco::where("controller", "=", $this->controller)->where("action", "=", $this->action)->firstOrFail()->id;
	}
	
	//call controller scanner
	public function scan($exceptControllers = array(), $exceptActions = array()){	
		return Scanner::scan($exceptControllers, $exceptActions);
	}
	
	//set default state
	public function defaultPermission($permission = false){
		$this->allowed = $permission;	
	}
	
	//set permission
	public function set($acoID = 0, $aroID = 0, $permission = false){
		if($acoID == 0 || $aroID == 0){
			return false;
		}else{
			$record = \DB::table("acl_aco_aro")->where("aco_id", "=", $acoID)->where("aro_id", "=", $aroID);
			if(!$record->get()){
				\DB::table("acl_aco_aro")->insert(array("aco_id" => $acoID, "aro_id" => $aroID, "allowed" => $permission));
			}else{
				$record->update(array("allowed" => $permission));
			}
			return true;
		}
	}
	
	
	//add group
	public function addGroup($name = ""){
		if(empty($name)){
			return false;
		}else{
			Aro::firstOrCreate(array("name"=>$name));
			return true;
		}
	}
	
	//get list of all groups (AROS)
	public function getGroups(){
		return Aro::all();
	}
	
	//get specific group (ARO)
	public function getGroup($id){
		return Aro::find($id);	
	}
	
	//get list of all controllers and actions
	public function getResources(){
		return Aco::all();
	}
	
	//get specific controller or action
	public function getResource($id){
		return Aco::find($id);
	}
	
		
}