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

        $package
            ->name('dashed-ecommerce-kiyoh');

        cms()->builder('plugins', [
            new DashedEcommerceKiyohPlugin(),
        ]);
    }
}
