<?php

namespace Dashed\DashedEcommerceKiyoh\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Dashed\DashedEcommerceKiyoh\DashedEcommerceKiyoh
 */
class DashedEcommerceKiyoh extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dashed-ecommerce-kiyoh';
    }
}
