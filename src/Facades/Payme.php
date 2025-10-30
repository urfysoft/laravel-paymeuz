<?php

namespace Urfysoft\Payme\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Urfysoft\Payme\Payme
 */
class Payme extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Urfysoft\Payme\Payme::class;
    }
}
