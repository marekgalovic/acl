<?php
namespace Marks\Acl\Console;

use Illuminate\Console\Command,
	Marks\Acl\Resources\ControllerScanner as Scanner;

class ScanActionsCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'acl:scan';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Scan all controllers';

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
		$this->info("Scanning.......");
		$list = Scanner::scan();
		foreach($list as $controller => $actions){
			$this->info("Controller: ".$controller);
			foreach($actions as $action){
				$this->info("\tAction: ".$action);
			}
		}
	}

}
