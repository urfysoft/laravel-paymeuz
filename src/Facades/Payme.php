<?php

namespace Urfysoft\Payme\Facades;

use Illuminate\Support\Facades\Facade;
use Urfysoft\Payme\PaymeSdk;

/**
 * @see PaymeSdk
 */
class Payme extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'urfysoft-payme';
    }
}
