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
	private $permissions;
	
	//configuration
	private $maxGroups = 4;
	private $userModel = "User";
	
	public function __construct(){
		$this->permissions = $this->getAll();
		$this->loadConfig();
	}
	
	//load configuration
	private function loadConfig(){
		$this->maxGroups = \Config::get("acl.max_groups");
	}
	
	//identify user
	public function identify($id){
		$this->aroID = $id;
	}
	
	//return if is allowed
	public function allowed(){
		$this->loadRoute();
		return $this->check();	
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
	public function set($acoID = 0, $aroID = 0, $permission){
		if($acoID == 0 || $aroID == 0){
			return false;
		}else{
			$record = \DB::table("acl_aco_aro")->where("aco_id", "=", $acoID)->where("aro_id", "=", $aroID);
			if(!$record->get()){
				\DB::table("acl_aco_aro")->insert(array("aco_id" => $acoID, "aro_id" => $aroID, "allowed" => $permission));
			}else{
				\DB::table("acl_aco_aro")->where("aco_id", "=", $acoID)->where("aro_id", "=", $aroID)->update(array("allowed" => $permission));
			}
			return true;
		}
	}
	
	
	//add group
	public function addGroup($name = "", $default){
		if(Aro::count() < $this->maxGroups){
			if($default === true){
				$this->removeDefaultGroup();
			}	
			if(empty($name)){
				return false;
			}else{
				Aro::firstOrCreate(array("name"=>$name, "isdefault"=>$default));
				return true;
			}
		}else{
			return false;
		}
	}
	
	public function editGroup($id, $data = array()){
		if($data["default"] === true){
			$this->removeDefaultGroup();
		}
		if(Aro::find($id)->update(array("name"=>$data["name"], "isdefault"=>$data["default"]))){
			return true;
		}
		return false;
	}
	
	public function deleteGroup($id){
		$group = $this->getGroup($id);
		if($group->isdefault == true){
			return false;	
		}else{
			$defaultID = $this->getDefault()->id;
			$model = $this->userModel;
			$model::where("grp", "=", $group->id)->update(array("grp"=>$defaultID));
			if($group->destroy($id)){
				return true;
			}else{
				return false;
			}
		}
	}
	
	public function removeDefaultGroup(){
		Aro::where("isdefault", "=", true)->update(array("isdefault"=>false));
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
	
	//get default group
	public function getDefault(){
		return Aro::where("isdefault", "=", true)->first();
	}
	
	//get string of group model
	public function group(){
		return "Marks\Acl\Models\Aro";
	}
	
	//get all permissions
	public function getAll(){
		$permissions = array();
		$data = \DB::table("acl_aco_aro")->get();	
		foreach($data as $permission){
			$permissions[$permission->aco_id][$permission->aro_id] = $permission->allowed;
		}
		return $permissions;
	}
	
	//get from permissions array
	public function getPermission($acoID = 0, $aroID = 0){
		if(isset($this->permissions[$acoID][$aroID])){
			return $this->permissions[$acoID][$aroID];
		}else{
			return false;
		}
	}
	
		
}