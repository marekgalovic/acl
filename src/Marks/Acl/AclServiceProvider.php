<?php namespace Marks\Acl;

use Illuminate\Support\ServiceProvider,
	Illuminate\Foundation\Artisan,
	Illuminate\Foundation\AliasLoader;


class AclServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	 public function boot(){
		 $this->package("marks/acl");
	 }
	 
	
	public function register()
	{
		$this->registerCommands();
		$this->app["acl"] = $this->app->share(function($app){
			return $app->make("Marks\Acl\AclClass");
		});
	}
	
	
	//register commands
	public function registerCommands(){
		//register setup command
		$this->app['command.acl.setup'] = $this->app->share(function($app){
			return new \Marks\Acl\Console\SetupAclCommand;
		});
		$this->commands('command.acl.setup');
		//register scan controllers command
		$this->app['command.acl.scan-actions'] = $this->app->share(function($app){
			return new \Marks\Acl\Console\ScanActionsCommand;
		});
		$this->commands('command.acl.scan-actions');
		//seeding command
		$this->app['command.acl.seed-initial'] = $this->app->share(function($app){
			return new \Marks\Acl\Console\SeedCommand;
		});
		$this->commands("command.acl.seed-initial");
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array("acl");
	}

}
