<?php

namespace Marks\Acl\Models;

class Aco extends \Eloquent
{
	
	public $timestamps = false;
	
	protected $table = "acl_acos";
	
	public $fillable = array("controller", "action", "prefix");
	
	public function getAros(){
		return $this->belongsToMany("Marks\Acl\Models\Aro", "acl_aco_aro", "aco_id", "id");
	}
}