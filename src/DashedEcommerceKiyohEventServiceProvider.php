<?php

namespace Dashed\DashedEcommerceKiyoh;

use Dashed\DashedEcommerceCore\Events\Orders\OrderMarkedAsPaidEvent;
use Dashed\DashedEcommerceKiyoh\Listeners\SendReviewEmailListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class DashedEcommerceKiyohEventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderMarkedAsPaidEvent::class => [
            SendReviewEmailListener::class,
        ],
    ];
}
