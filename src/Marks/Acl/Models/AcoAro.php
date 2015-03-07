<?php

namespace Marks\Acl\Models;

class AcoAro extends \Eloquent
{
	protected $table = "acl_aco_aro";
	
	public $timestamps = false;

	public $fillable = array("aco_id", "aro_id", "type", "allowed");

	public function aco()
	{
		return $this->hasOne("Marks\Acl\Models\Aco", "id", "aco_id");
	}

	public function aro()
	{
		return $this->hasOne("Marks\Acl\Models\Aro", "id", "aco_id");
	}
}