<?php
namespace Marks\Acl\Models;

class Aro extends \Eloquent{
	
	protected $table = "acl_aro_groups";
	
	public $fillable = array("name", "isdefault", "type");
	
	public function getAcos(){
		return $this->belongsToMany("Marks\Acl\Models\Aco", "acl_aco_aro", "aro_id", "id");
	}
	
	public function users(){
		$acl = \App::make("acl");
		$type = $acl->type;
		$config = $acl->config;
		return $this->hasMany("\\".$config[$type]["model"]."", $config[$type]["col"], "id");
	}
}