<?php

namespace Debutify\Shopify\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Shopify FACADE
 */
class Shopify extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'shopify';
    }
}
