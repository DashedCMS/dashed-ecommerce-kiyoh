<?php

namespace Dashed\DashedEcommerceKiyoh;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Dashed\DashedEcommerceKiyoh\Filament\Pages\Settings\KiyohSettingsPage;

class DashedEcommerceKiyohPlugin implements Plugin
{
    public function getId(): string
    {
        return 'dashed-ecommerce-kiyoh';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                KiyohSettingsPage::class,
            ]);
    }

    public function boot(Panel $panel): void
    {

    }
}
