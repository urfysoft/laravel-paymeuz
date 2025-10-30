<?php

namespace Urfysoft\Payme;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Urfysoft\Payme\Commands\PaymeCommand;

class PaymeServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-paymeuz')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel_paymeuz_table')
            ->hasCommand(PaymeCommand::class);
    }
}
