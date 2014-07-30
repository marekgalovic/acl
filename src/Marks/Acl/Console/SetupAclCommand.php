<?php

namespace Marks\Acl\Console;

use Illuminate\Console\Command,
	Marks\Acl\ControllerScanner as Scanner;

class SetupAclCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'acl:setup';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Initial acl configuration';

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
		$this->info("Setup started");
		$this->info("Database configuration...");
		\Artisan::call("migrate", array("--path" => "vendor/marks/acl/src/migrations"));
		$this->info("Done ....");
		$this->info("Controllers scanning...");
		\Artisan::call("acl:scan");
		$this->info("Done ....");
	}
	

}
