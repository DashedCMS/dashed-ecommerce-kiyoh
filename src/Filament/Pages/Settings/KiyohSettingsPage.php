<?php

namespace Dashed\DashedEcommerceKiyoh\Filament\Pages\Settings;

use Filament\Pages\Page;
use Filament\Forms\Components\Tabs;
use Dashed\DashedCore\Classes\Sites;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use Dashed\DashedCore\Models\Customsetting;
use Dashed\DashedEcommerceKiyoh\Classes\Kiyoh;

class KiyohSettingsPage extends Page
{
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $title = 'Kiyoh';

    protected static string $view = 'dashed-core::settings.pages.default-settings';
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

    protected function getFormSchema(): array
    {
        $sites = Sites::getSites();
        $tabGroups = [];

        $tabs = [];
        foreach ($sites as $site) {
            $schema = [
                Placeholder::make('label')
                    ->label("Kiyoh voor {$site['name']}")
                    ->content('Activeer kiyoh zodat de klanten automatisch een mail krijgen om een review achter te laten.')
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                Placeholder::make('label')
                    ->label("Kiyoh is " . (! Customsetting::get('kiyoh_connected', $site['id'], 0) ? 'niet' : '') . ' geconnect')
                    ->content(Customsetting::get('kiyoh_connection_error', $site['id'], ''))
                    ->columnSpan([
                        'default' => 1,
                        'lg' => 2,
                    ]),
                TextInput::make("kiyoh_api_key_{$site['id']}")
                    ->label('Kiyoh API key')
                    ->maxLength(255),
                TextInput::make("kiyoh_location_id_{$site['id']}")
                    ->label('Kiyoh location ID')
                    ->maxLength(255),
                TextInput::make("kiyoh_delay_{$site['id']}")
                    ->label('Kiyoh delay')
                    ->helperText('Aantal dagen na betaling dat de review mail verstuurd wordt.')
                ->numeric()
                ->minValue(0)
                ->maxValue(365 * 5),
            ];

            $tabs[] = Tab::make($site['id'])
                ->label(ucfirst($site['name']))
                ->schema($schema)
                ->columns([
                    'default' => 1,
                    'lg' => 2,
                ]);
        }
        $tabGroups[] = Tabs::make('Sites')
            ->tabs($tabs);

        return $tabGroups;
    }

    public function getFormStatePath(): ?string
    {
        return 'data';
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
            ->title('De Kiyoh instellingen zijn opgeslagen')
            ->success()
            ->send();

        return redirect(KiyohSettingsPage::getUrl());
    }
}
