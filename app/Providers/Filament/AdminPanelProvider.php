<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Dashboard;
use App\Filament\Pages\SystemSettings;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentColor;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Modules\User\Models\User;
use Rmsramos\Activitylog\ActivitylogPlugin;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;
use Tapp\FilamentAuthenticationLog\FilamentAuthenticationLogPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function boot()
    {
        Table::configureUsing(function (Table $table) {
            $table->paginated([10, 25, 50, 100])
                ->defaultPaginationPageOption(10)
                ->defaultSort('id', 'desc');
        });

        Repeater::configureUsing(function (Repeater $repeater) {
            $repeater->addActionLabel('Add');
        });

        MarkdownEditor::configureUsing(function (MarkdownEditor $editor) {
            $editor->disableToolbarButtons([
                'blockquote',
                'strike',
                'codeBlock',
            ]);
        });

        RichEditor::configureUsing(function (RichEditor $editor) {
            $editor->disableToolbarButtons([
                'blockquote',
                'codeBlock',
            ]);
        });

        SpatieMediaLibraryImageColumn::configureUsing(function (SpatieMediaLibraryImageColumn $column) {
            $column->placeholder('-')
                ->alignCenter();
        });

        SelectFilter::configureUsing(function (SelectFilter $filter) {
            $filter->native(false);
        });

        Toggle::configureUsing(function (Toggle $toggle) {
            $toggle->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark');
        });

        Select::configureUsing(function (Select $select) {
            $select->native(false);
        });

        ToggleColumn::configureUsing(function (ToggleColumn $column) {
            $column->onIcon('heroicon-s-check')
                ->offIcon('heroicon-s-x-mark')
                ->alignCenter();
        });

        DatePicker::configureUsing(function (DatePicker $datepicker) {
            $datepicker->native(false)
                ->timezone($this->timezone())
                ->displayFormat('d/m/Y')
                ->closeOnDateSelection();
        });

        DateTimePicker::configureUsing(function (DateTimePicker $component): void {
            $component->native(false)
                ->timezone($this->timezone())
                ->displayFormat('d/m/Y H:i:s');
        });

        TextColumn::configureUsing(function (TextColumn $component): void {
            $component->timezone($this->timezone());
        });

        ExportBulkAction::configureUsing(function (ExportBulkAction $bulkAction) {
            $bulkAction->deselectRecordsAfterCompletion()
                ->closeModalByEscaping(false)
                ->closeModalByClickingAway(false)
                ->formats([
                    ExportFormat::Xlsx,
                ]);
        });

        DateRangeFilter::configureUsing(function (DateRangeFilter $dateRangeFilter) {
            $dateRangeFilter->autoApply();
        });

        FilamentColor::register([
            'primary' => [
                50 => '#fdf7ef',
                100 => '#faebda',
                200 => '#f4d5b4',
                300 => '#edb884',
                400 => '#e49153',
                500 => '#df7a39',
                600 => '#cf5d27',
                700 => '#ac4722',
                800 => '#8a3a22',
                900 => '#6f321f',
                950 => '#3c170e',
            ],

            'secondary' => [
                50 => '#f1f9fe',
                100 => '#e3f1fb',
                200 => '#c0e4f7',
                300 => '#88cff1',
                400 => '#3fb3e6',
                500 => '#219ed6',
                600 => '#137fb6',
                700 => '#116593',
                800 => '#12567a',
                900 => '#154865',
                950 => '#0e2e43',
            ],

            'gray' => [
                50 => '#f3f7f8',
                100 => '#dfeaee',
                200 => '#c3d6de',
                300 => '#99b9c7',
                400 => '#6894a8',
                500 => '#4d788d',
                600 => '#426278',
                700 => '#384f5f',
                800 => '#364754',
                900 => '#303d49',
                950 => '#1d262f',
            ],

            ...Color::all(),
        ]);
    }

    protected function timezone(): string
    {
        return config('app.timezone');
    }

    public function register(): void
    {
        $this->app->bind(Authenticatable::class, User::class);

        parent::register();
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->passwordReset()
            ->sidebarCollapsibleOnDesktop()
            ->unsavedChangesAlerts()
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->maxContentWidth(MaxWidth::Full)
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
            ->font('Plus Jakarta Sans')
            ->favicon('/images/favicon/favicon.ico')
            ->brandName('Greenhat')
            ->brandLogoHeight('50px')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->pages([
                Dashboard::class,
                SystemSettings::class,
            ])
            ->navigationGroups([
                'Staff',
                'Settings',
                'Logs',
            ])
            ->plugins([
                TwoFactorAuthenticationPlugin::make()
                    ->addTwoFactorMenuItem()
                    ->enforceTwoFactorSetup(config('auth.two_factor.enabled')),
                ActivitylogPlugin::make()
                    ->pluralLabel('Activities')
                    ->navigationGroup('Logs'),
                FilamentAuthenticationLogPlugin::make(),
            ])
            ->widgets([
                //
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
