<?php

/* Acl laravel package by Marek Galovic */

namespace Marks\Acl;

use Marks\Acl\ControllerScanner as Scanner;
use	Marks\Acl\Models\Aco as Aco;
use	Marks\Acl\Models\Aro as Aro;
use	Marks\Acl\Models\AcoAro as AcoAro;

class AclClass{
	
	private $route;
	private $controller;
	private $action;
	
	private $acoID = 0;
	private $aroID = 0;
	private $allowed = false;
	private $permissions;
	public $type;
	
	//configuration
	private $maxGroups = 4;
	public $config = array();
	
	
	public function __construct(){
		$this->permissions = $this->getAll();
		$this->loadConfig();
	}
	
	public function __call($type, $args){
		$this->type = strtolower($type);
		return $this;
	}
	
	//load configuration
	private function loadConfig(){
		$this->maxGroups = \Config::get("acl.max_groups");
		$this->config = \Config::get("acl");
	}
	
	public function getZones(){
		return $this->config["zones"];
	}
	
	public function getDefaultZone(){
		return $this->config["default_zone"];
	}
	
	//identify user
	public function identify($id){
		$this->aroID = $id;
		return $this;
	}
	
	//check if user is allowed to perform action
	public function check(){
		$this->loadRoute();
		if($allowed = AcoAro::where("aro_id", "=", $this->aroID)->where("aco_id", "=", $this->acoID)->where("type","=", $this->type)->first()){
			$this->allowed = $allowed->allowed;
		}
		return $this->allowed;	
	}
	
	private function loadRoute(){
		$this->route = explode("@",\Route::currentRouteAction());
		$this->controller = $this->route[0];
		preg_match('/(?:get|post)(.*)/', end($this->route), $routeMatch);
		$this->action = end($routeMatch);
		try{
			$this->acoID = Aco::where("controller", "=", $this->controller)->where("action", "=", $this->action)->firstOrFail()->id;
		}catch(\Exception $e){
			throw new \Exception("Route[{$this->controller}@{$this->action}] not found");
		}
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
			if($record = AcoAro::where("aco_id", "=", $acoID)->where("aro_id", "=", $aroID)->where("type", "=", $this->type)->first()){
				$record->fill(array("allowed" => $permission));
			}else{
				$record = new AcoAro;
				$record->fill(array("aco_id" => $acoID, "aro_id" => $aroID, "allowed" => $permission, "type" => $this->type));
			}
			return $record->save();
		}
		return false;
	}
	
	
	//add group
	public function addGroup($name = "", $default = false){
		if((Aro::where("type","=", $this->type)->count() < $this->maxGroups)&&(!empty($name))){
			if($default === true){
				$this->removeDefaultGroup();
			}	
			$aro = new Aro(array("name"=>$name, "type"=>$this->type, "isdefault"=>$default));
			return $aro->save();
		}
			return false;
	}
	
	public function editGroup($id, $data = array()){
		if($data["default"] === true){
			$this->removeDefaultGroup();
		}
		if($group = Aro::find($id)){
			$group->fill(update(array("name"=>$data["name"], "type"=>$this->type, "isdefault"=>$data["default"])));
			return $group->save();
		}
		return false;
	}
	
	public function deleteGroup($id){
		$group = $this->getGroup($id);
		if($group->isdefault == true){
			return false;	
		}else{
			$defaultID = $this->getDefault()->id;
			$model = $this->config[$this->type]["model"];
			$col = $this->config[$this->type]["col"];
			$model::where($col, "=", $group->id)->update(array($col=>$defaultID));
			if($group->destroy($id)){
				return true;
			}else{
				return false;
			}
		}
	}
	
	public function removeDefaultGroup(){
		Aro::where("isdefault", "=", true)->where("type", "=", $this->type)->update(array("isdefault"=>false));
	}
	
	//get list of all groups (AROS)
	public function getGroups(){
		return Aro::where("type", "=", $this->type)->get();
	}
	
	//get specific group (ARO)
	public function getGroup($id){
		return Aro::where("type", "=", $this->type)->find($id);
	}
	
	//get list of all controllers and actions
	public function getResources($prefix = null){
		if($prefix != null){
			if(!is_array($prefix)){
				$prefix = array($prefix);
			}
			return Aco::whereIn("prefix", $prefix)->get();
		}
		return Aco::all();
	}
	
	//get specific controller or action
	public function getResource($id){
		return Aco::find($id);
	}
	
	//get default group
	public function getDefault(){
		return Aro::where("isdefault", "=", true)->where("type", "=", $this->type)->first();
	}
	
	//get string of group model
	public function groupModel()
	{
		return "Marks\Acl\Models\Aro";
	}

	public function resourceModel()
	{
		return "Marks\Acl\Models\Aco";
	}

	public function pivotTable()
	{
		return "acl_aco_aro";
	}
	
	//get all permissions
	public function getAll(){
		$permissions = array();
		$data = AcoAro::all();	
		foreach($data as $permission){
			$permissions[$permission->type][$permission->aco_id][$permission->aro_id] = $permission->allowed;
		}
		return $permissions;
	}
	
	//get from permissions array
	public function getPermission($acoID = 0, $aroID = 0){
		if(isset($this->permissions[$this->type][$acoID][$aroID])){
			return $this->permissions[$this->type][$acoID][$aroID];
		}else{
			return false;
		}
	}
	
		
}