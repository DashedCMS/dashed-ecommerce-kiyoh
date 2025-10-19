<?php

namespace Dashed\DashedEcommerceKiyoh\Classes;

use Dashed\DashedCore\Classes\Sites;
use Illuminate\Support\Facades\Http;
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

        $apiKey = Customsetting::get('kiyoh_api_key', $siteId);
        $locationId = Customsetting::get('kiyoh_location_id', $siteId);
        $delay = Customsetting::get('kiyoh_delay', $siteId);

        if (! $apiKey || ! $locationId) {
            return;
        }

        return [
            'apiKey' => $apiKey,
            'locationId' => $locationId,
            'delay' => $delay,
            'baseUrl' => 'https://www.kiyoh.com',
        ];
    }

    public static function isConnected($siteId = null)
    {
        if (! $siteId) {
            $siteId = Sites::getActive();
        }

        $kiyohClient = self::initialize($siteId);

        if (! $kiyohClient) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'X-Publication-Api-Token' => $kiyohClient['apiKey'],
                'Accept' => 'application/json',
            ])->post("{$kiyohClient['baseUrl']}/v1/invite/external", [
                'location_id' => $kiyohClient['locationId'],
                'first_name' => 'Voornaam',
                'last_name' => 'Achternaam',
                'invite_email' => 'noreply@example.com',
                'delay' => 0,
                'ref_code' => 'Test invite',
                'language' => 'nl',
            ])
                ->json();

            if (($response['code'] ?? '') == 'OK' || ($response['detailedError'][0]['errorCode'] ?? '') == 'INVITATION_ALREADY_PLACED') {
                Customsetting::set('kiyoh_connection_error', null, $siteId);
                Customsetting::set('kiyoh_connected', true, $siteId);

                return true;
            } else {
                Customsetting::set('kiyoh_connection_error', $response['errorCode'] ?? 'Onbekende reden', $siteId);
                Customsetting::set('kiyoh_connected', false, $siteId);

                return false;
            }
        } catch (Client\Exception $e) {
            Customsetting::set('kiyoh_connection_error', $e->getMessage(), $siteId);
            Customsetting::set('kiyoh_connected', false, $siteId);

            return false;
        }
    }

    public static function sendReviewEmail(Order $order)
    {
        $kiyohClient = self::initialize();

        if ($kiyohClient && $order->email) {

            try {
                $response = Http::withHeaders([
                    'X-Publication-Api-Token' => $kiyohClient['apiKey'],
                    'Accept' => 'application/json',
                ])->post("{$kiyohClient['baseUrl']}/v1/invite/external", [
                    'location_id' => $kiyohClient['locationId'],
                    'first_name' => $order->first_name,
                    'last_name' => $order->last_name,
                    'invite_email' => $order->email,
                    'ref_code' => $order->invoice_id,
                    'delay' => $kiyohClient['delay'] ?? 0,
                    'language' => 'nl',
                ])
                    ->json();

                if (($response['code'] ?? '') == 'OK') {
                    OrderLog::createLog(orderId: $order->id, note: 'Kiyoh uitnodiging verstuurd');
                } else {
                    OrderLog::createLog(orderId: $order->id, note: 'Kiyoh uitnodiging NIET verstuurd om reden: ' . ($response['errorCode'] ?? 'Onbekende reden'));
                }

            } catch (Client\Exception $e) {
                OrderLog::createLog(orderId: $order->id, note: 'Kiyoh uitnodiging NIET verstuurd om reden: ' . $e->getMessage());
            }
        }
    }
}
