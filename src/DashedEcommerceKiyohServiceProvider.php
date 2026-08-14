<?php

namespace Dashed\DashedEcommerceKiyoh;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Dashed\DashedEcommerceKiyoh\Filament\Pages\Settings\KiyohSettingsPage;

class DashedEcommerceKiyohServiceProvider extends PackageServiceProvider
{
    public static string $name = 'dashed-ecommerce-kiyoh';

    public function configurePackage(Package $package): void
    {
        cms()->registerSettingsPage(KiyohSettingsPage::class, 'Kiyoh', 'chat-bubble-left-ellipsis', 'Koppel Kiyoh');

        if (method_exists(cms(), 'registerIntegration')) {
            cms()->registerIntegration([
                'slug' => 'kiyoh',
                'label' => 'Kiyoh',
                'icon' => 'heroicon-o-star',
                'category' => 'reviews',
                'settings_page' => KiyohSettingsPage::class,
                'health_check' => fn (?string $siteId = null) => \Dashed\DashedCore\Integrations\IntegrationHealth::fromSettings(['kiyoh_api_key', 'kiyoh_location_id'], $siteId, 'API key of location ID ontbreekt'),
                'package' => 'dashed-ecommerce-kiyoh',
            ]);
        }

        cms()->registerSettingsDocs(
            page: \Dashed\DashedEcommerceKiyoh\Filament\Pages\Settings\KiyohSettingsPage::class,
            title: 'Kiyoh instellingen',
            intro: 'Op deze pagina koppel je jouw webshop aan Kiyoh. Met deze koppeling worden er na een aankoop automatisch review-verzoeken naar je klanten gestuurd, zodat zij een beoordeling kunnen achterlaten over hun bestelling. Werk je met meerdere sites? Dan kun je per site een eigen Kiyoh account en locatie koppelen.',
            sections: [
                [
                    'heading' => 'Wat kun je hier instellen?',
                    'body' => <<<MARKDOWN
Op deze pagina regel je drie dingen:

- De API key waarmee jouw webshop verbinding maakt met Kiyoh.
- De locatie binnen je Kiyoh account waar de reviews aan gekoppeld worden.
- Het aantal dagen dat er gewacht wordt voordat de review-uitnodiging verstuurd wordt.
MARKDOWN,
                ],
                [
                    'heading' => 'Hoe zet je dit op?',
                    'body' => <<<MARKDOWN
1. Log in op je Kiyoh dashboard.
2. Ga naar de API instellingen en kopieer je API key.
3. Zoek het locatie ID op van de shop waarvoor je reviews wilt verzamelen. Een Kiyoh account kan namelijk meerdere locaties of shops bevatten, dus controleer of je het juiste ID gebruikt.
4. Plak de API key en het locatie ID op deze pagina.
5. Stel eventueel een vertraging in (in dagen) voordat de review-mail verstuurd wordt.
6. Sla de instellingen op.
MARKDOWN,
                ],
            ],
            fields: [
                'API key' => 'De API key uit je Kiyoh dashboard. Deze sleutel zorgt voor de verbinding tussen jouw webshop en Kiyoh.',
                'Locatie ID' => 'Het ID van de locatie binnen je Kiyoh account. Heb je meerdere shops of vestigingen in Kiyoh, kies dan het juiste ID zodat reviews op de juiste plek terechtkomen.',
                'Vertraging (dagen)' => 'Het aantal dagen na de betaling waarna de review-uitnodiging wordt verstuurd. Vul 0 in als je de mail direct na betaling wilt versturen. Maximaal 1825 dagen (vijf jaar).',
            ],
            tips: [
                'Kies een vertraging die past bij je gemiddelde levertijd. Zo vraag je pas een review nadat je klant het product daadwerkelijk heeft kunnen ervaren.',
                'Controleer altijd of het locatie ID klopt voordat je live gaat. Een verkeerd ID zorgt ervoor dat reviews op een andere shop terechtkomen.',
            ],
        );

        $package
            ->hasViews()
            ->name('dashed-ecommerce-kiyoh');

        cms()->builder('plugins', [
            new DashedEcommerceKiyohPlugin(),
        ]);
    }

    public function bootingPackage()
    {
        // Twee guards: dashed-core kan ouder zijn en emailBlock nog niet
        // kennen, en een site zonder nieuwsbriefmodule heeft dit blok
        // nergens voor nodig. In bootingPackage() en niet in
        // configurePackage(): die laatste draait in de register-fase, en
        // dashed-ecommerce-kiyoh komt daarin vóór dashed-newsletter, dus de
        // binding bestaat op dat moment nog niet.
        if (method_exists(cms(), 'emailBlock') && app()->bound('newsletter')) {
            cms()->emailBlock('kiyoh-score', \Dashed\DashedEcommerceKiyoh\Mail\EmailBlocks\ReviewScoreBlock::class);
        }
    }
}
