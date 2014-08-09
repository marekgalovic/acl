<?php
namespace Marks\Acl\Console;

use Illuminate\Console\Command,
	Marks\Acl\Models\Aro as Aro,
	Marks\Acl\Models\Aco as Aco;

class SeedCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'acl:seed';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Seed initial data';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		//
		$this->info("Creating initial admin group");
		$defaultZone = \Config::get("acl.default_zone");
		Aro::firstOrCreate(array(
			"name"=>"Default",
			"isdefault"=>true,
			"type"=>$defaultZone,
		));
		$this->info("Allowing initial group to access everything");
		$acos = Aco::all();
		foreach($acos as $aco){
			\DB::table("acl_aco_aro")->insert(array(
				"aco_id"=>$aco->id,
				"aro_id"=>1,
				"allowed"=>1,
				"type"=> $defaultZone,
			));
		}
	}

}
