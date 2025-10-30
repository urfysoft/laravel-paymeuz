<?php

namespace Urfysoft\Payme;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->discoversMigrations();

        $this->app->singleton(PaymeSdk::class, function ($app) {
            $config = $app['config']['paymeuz'];

            return new PaymeSdk(
                merchantId: $config['merchant_id'],
                secretKey: $config['secret_key'],
                baseUrl: $config['base_url'],
                timeout: $config['timeout'],
                loggingEnabled: $config['logging']['enabled'],
                loggingChannel: $config['logging']['channel']
            );
        });

        $this->app->alias(PaymeSdk::class, 'urfysoft-payme');
    }
}
