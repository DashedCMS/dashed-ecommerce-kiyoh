<?php

namespace Dashed\DashedEcommerceKiyoh\Classes;

use WebwinkelKeur\Client;
use WebwinkelKeur\Client\Request;
use Dashed\DashedCore\Classes\Sites;
use Dashed\DashedCore\Models\Customsetting;
use Dashed\DashedEcommerceCore\Models\Order;
use Dashed\DashedEcommerceCore\Models\OrderLog;

class Kiyoh
{
    public static function initialize($siteId = null)
    {
        if (! $siteId) {
            $siteId = Sites::getActive();
        }

        $clientId = Customsetting::get('kiyoh_client_id', $siteId);
        $authToken = Customsetting::get('kiyoh_auth_token', $siteId);

        if (! $clientId && ! $authToken) {
            return;
        }

        $webwinkelKeurClient = new Client($clientId, $authToken);

        return $webwinkelKeurClient;
    }

    public static function isConnected($siteId = null)
    {
        if (! $siteId) {
            $siteId = Sites::getActive();
        }

        $webwinkelKeurClient = self::initialize($siteId);

        if (! $webwinkelKeurClient) {
            return false;
        }

        try {
            $webshop = $webwinkelKeurClient->getWebshop();
            Customsetting::set('kiyoh_connection_error', null, $siteId);

            return true;
        } catch (Client\Exception $e) {
            Customsetting::set('kiyoh_connection_error', $e->getMessage(), $siteId);
            Customsetting::set('kiyoh_connected', false, $siteId);

            return false;
        }
    }

    public static function sendReviewEmail(Order $order)
    {
        if (self::isConnected($order->site_id) && $order->email) {
            $webwinkelKeurClient = self::initialize();
            $invitation = new Request\Invitation();
            $invitation
                ->setCustomerName($order->name)
                ->setEmailAddress($order->email)
                ->setOrderNumber($order->invoice_id)
                ->setOrderTotal($order->total);

            if ($order->phone_number) {
                $invitation->setPhoneNumbers([$order->phone_number]);
            }

            try {
                $webwinkelKeurClient->sendInvitation($invitation);
                OrderLog::createLog(orderId: $order->id, note: 'Kiyoh uitnodiging verstuurd');
            } catch (Client\Exception $e) {
                OrderLog::createLog(orderId: $order->id, note: 'Kiyoh uitnodiging NIET verstuurd');
            }
        }
    }
}
