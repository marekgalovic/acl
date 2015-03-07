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
			$t->string("type");
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
		Schema::drop("acl_aco_aro");
	}

}
