<?php

namespace Dashed\DashedEcommerceKiyoh\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Dashed\DashedEcommerceKiyoh\Classes\Kiyoh;
use Dashed\DashedEcommerceCore\Events\Orders\OrderMarkedAsPaidEvent;

class SendReviewEmailListener implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

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
