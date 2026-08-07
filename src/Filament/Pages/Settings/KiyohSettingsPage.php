<?php

namespace Dashed\DashedEcommerceKiyoh\Filament\Pages\Settings;

use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Dashed\DashedCore\Classes\Sites;
use Filament\Schemas\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Tabs\Tab;
use Dashed\DashedCore\Models\Customsetting;
use Filament\Infolists\Components\TextEntry;
use Dashed\DashedEcommerceKiyoh\Classes\Kiyoh;
use Dashed\DashedCore\Traits\HasSettingsPermission;

class KiyohSettingsPage extends Page
{
    use HasSettingsPermission;

    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Kiyoh';

    protected string $view = 'dashed-core::settings.pages.default-settings';
    public array $data = [];

    public function mount(): void
    {
        $formData = [];
        $sites = Sites::getSites();
        foreach ($sites as $site) {
            $formData["kiyoh_api_key_{$site['id']}"] = Customsetting::get('kiyoh_api_key', $site['id']);
            $formData["kiyoh_location_id_{$site['id']}"] = Customsetting::get('kiyoh_location_id', $site['id']);
            $formData["kiyoh_delay_{$site['id']}"] = Customsetting::get('kiyoh_delay', $site['id'], 0);
            $formData["kiyoh_connected_{$site['id']}"] = Customsetting::get('kiyoh_connected', $site['id'], 0);
            $formData["kiyoh_connection_error_{$site['id']}"] = Customsetting::get('kiyoh_connection_error', $site['id'], '');
        }

        $this->form->fill($formData);
    }

    public function form(Schema $schema): Schema
    {
        $sites = Sites::getSites();
        $tabGroups = [];

        $tabs = [];
        foreach ($sites as $site) {
            $newSchema = [
                TextEntry::make('label')
                    ->state("Kiyoh voor {$site['name']}")
                    ->state('Activeer kiyoh zodat de klanten automatisch een mail krijgen om een review achter te laten.')
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                TextEntry::make('label')
                    ->state("Kiyoh is " . (! Customsetting::get('kiyoh_connected', $site['id'], 0) ? 'niet' : '') . ' geconnect')
                    ->state(Customsetting::get('kiyoh_connection_error', $site['id'], ''))
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                TextInput::make("kiyoh_api_key_{$site['id']}")
                    ->label(__('Kiyoh API key'))
                    ->maxLength(255),
                TextInput::make("kiyoh_location_id_{$site['id']}")
                    ->label(__('Kiyoh location ID'))
                    ->maxLength(255),
                TextInput::make("kiyoh_delay_{$site['id']}")
                    ->label(__('Kiyoh delay'))
                    ->helperText(__('Aantal dagen na betaling dat de review mail verstuurd wordt.'))
                ->numeric()
                ->minValue(0)
                ->maxValue(365 * 5),
            ];

            $tabs[] = Tab::make($site['id'])
                ->label(ucfirst($site['name']))
                ->schema($newSchema)
                ->columns([
                    'default' => 1,
                    'lg' => 2,
                ]);
        }
        $tabGroups[] = Tabs::make('Sites')
            ->tabs($tabs);

        return $schema->schema($tabGroups)
            ->statePath('data');
    }

    public function submit()
    {
        $sites = Sites::getSites();

        foreach ($sites as $site) {
            Customsetting::set('kiyoh_api_key', $this->form->getState()["kiyoh_api_key_{$site['id']}"], $site['id']);
            Customsetting::set('kiyoh_location_id', $this->form->getState()["kiyoh_location_id_{$site['id']}"], $site['id']);
            Customsetting::set('kiyoh_delay', $this->form->getState()["kiyoh_delay_{$site['id']}"], $site['id']);
            Customsetting::set('kiyoh_connected', Kiyoh::isConnected($site['id']), $site['id']);
        }

        Notification::make()
            ->title(__('De Kiyoh instellingen zijn opgeslagen'))
            ->success()
            ->send();

        return redirect(KiyohSettingsPage::getUrl());
    }
}
