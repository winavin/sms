<?php

declare(strict_types=1);

namespace Winavin\Sms\Facades;

use Illuminate\Support\Facades\Facade;

class Sms extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'sms';
    }
}
