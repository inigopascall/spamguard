<?php

namespace InigoPascall\SpamGuard\Facades;

use Illuminate\Support\Facades\Facade;

class SpamGuard extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'spamguard';
    }
}
