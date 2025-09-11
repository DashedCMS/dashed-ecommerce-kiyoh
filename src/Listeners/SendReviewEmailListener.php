<?php

namespace Dashed\DashedEcommerceKiyoh\Listeners;

use Dashed\DashedEcommerceKiyoh\Classes\Kiyoh;
use Dashed\DashedEcommerceCore\Events\Orders\OrderMarkedAsPaidEvent;

class SendReviewEmailListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(OrderMarkedAsPaidEvent $event)
    {
        Kiyoh::sendReviewEmail($event->order);
    }
}
