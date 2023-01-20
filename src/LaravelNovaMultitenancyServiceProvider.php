<?php

namespace HenryAvila\LaravelNovaMultitenancy;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelNovaMultitenancyServiceProvider extends PackageServiceProvider
{

	public function configurePackage(Package $package): void
	{
		/*
		 * This class is a Package Service Provider
		 *
		 * More info: https://github.com/spatie/laravel-package-tools
		 */
		$package
			->name('laravel-nova-multitenancy')
			->hasConfigFile()
			->hasViews()
			->hasRoute('web')
			->hasTranslations()
			->hasMigration('create_laravel_nova_multitenancy_tables');
		//			->hasCommand(LaravelNovaMultitenancyCommand::class)

//	    $this->publishes([
//		    __DIR__.'/../lang' => $this->app->langPath('vendor/courier'),
//	    ]);
	}
}
