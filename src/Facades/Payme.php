<?php

namespace Urfysoft\Payme\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Urfysoft\Payme\PaymeSdk
 */
class Payme extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Urfysoft\Payme\PaymeSdk::class;
    }
}
