<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AclCreateAclAcoAro extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//
		Schema::create("acl_aco_aro", function(Blueprint $t){
			$t->increments("id");
			$t->unsignedInteger("aco_id");
			$t->unsignedInteger("aro_id");
			$t->foreign("aco_id")->references("id")->on("acl_acos")->onUpdate("NO ACTION")->onDelete("NO ACTION");
			$t->foreign("aro_id")->references("id")->on("acl_aro_groups")->onUpdate("NO ACTION")->onDelete("NO ACTION");
			$t->boolean("allowed");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}
