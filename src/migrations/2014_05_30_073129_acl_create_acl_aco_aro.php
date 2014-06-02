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
			$t->integer("aco_id");
			$t->integer("aro_id");
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
