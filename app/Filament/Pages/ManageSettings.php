<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSettings extends Page
{
    protected string $view = 'filament.pages.manage-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Site Settings';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Site Settings';

    /** @var array<string, mixed> */
    public ?array $data = [];

    private const KEYS = [
        'site_name', 'tagline', 'phone', 'email', 'booking_notification_email',
        'address', 'hero_subheading',
        'facebook_url', 'instagram_url', 'whatsapp_number',
    ];

    public function mount(): void
    {
        $this->form->fill(
            collect(self::KEYS)->mapWithKeys(fn ($key) => [$key => Setting::get($key)])->all()
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Business details')
                    ->columns(2)
                    ->components([
                        TextInput::make('site_name')
                            ->label('Site name')
                            ->required(),
                        TextInput::make('tagline'),
                        TextInput::make('phone')
                            ->tel(),
                        TextInput::make('email')
                            ->label('Public email address')
                            ->email(),
                        TextInput::make('booking_notification_email')
                            ->label('Booking notification email')
                            ->helperText('New bookings, quotes, and contact messages are sent here')
                            ->email(),
                        TextInput::make('address'),
                    ]),
                Section::make('Homepage')
                    ->components([
                        Textarea::make('hero_subheading')
                            ->label('Hero subheading')
                            ->rows(2),
                    ]),
                Section::make('Social')
                    ->columns(2)
                    ->components([
                        TextInput::make('facebook_url')
                            ->label('Facebook URL')
                            ->url(),
                        TextInput::make('instagram_url')
                            ->label('Instagram URL')
                            ->url(),
                        TextInput::make('whatsapp_number')
                            ->label('WhatsApp number'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach (self::KEYS as $key) {
            Setting::set($key, $data[$key] ?? null);
        }

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
