<?php
namespace Marks\Acl\Models;

class Aro extends \Eloquent{
	
	protected $table = "acl_aro_groups";
	
	public $fillable = array("name", "isdefault");
	
	public function getAcos(){
		return $this->belongsToMany("Marks\Acl\Models\Aco", "acl_aco_aro", "aro_id", "id");
	}
	
	public function users(){
		$model = \Config::get("acl.user.model");
		$col = \Config::get("acl.user.col");
		return $this->hasMany("\\".$model."", $col, "id");
	}
}