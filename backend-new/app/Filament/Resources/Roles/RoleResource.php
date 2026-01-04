<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles;

use App\Models\Role;
use App\Models\User;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use App\Filament\Resources\Roles\Pages\CreateRole;
use App\Filament\Resources\Roles\Pages\EditRole;
use App\Filament\Resources\Roles\Pages\ListRoles;
use App\Filament\Resources\Roles\Pages\ViewRole;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;
use BezhanSalleh\PluginEssentials\Concerns\Resource as Essentials;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Panel;
use Filament\Notifications\Notification;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\FileUpload;
use UnitEnum;
use BackedEnum;

class RoleResource extends Resource
{
    use Essentials\BelongsToParent;
    use Essentials\BelongsToTenant;
    use Essentials\HasGlobalSearch;
    use Essentials\HasLabels;
    use Essentials\HasNavigation;
    use HasShieldFormComponents;

    protected static ?string $recordTitleAttribute = 'name';

    // Explicitly set navigation group
    protected static string|UnitEnum|null $navigationGroup = 'All Roles For User';

    protected static string|BackedEnum|null $navigationIcon = 'zondicon-shield';

    protected static ?string $navigationLabel = 'Roles';

    protected static ?string $modelLabel = 'Roles Management';

    protected static ?string $pluralModelLabel = 'Roles Management';

    public static function canCreate(): bool
    {
        return Gate::allows('Create:Role');
    }

    // Set navigation icon for the resource
    public static function getNavigationIcon(): ?string
    {
        return 'zondicon-shield';
    }

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Roles')
                                    ->required()
                                    ->maxLength(255),

                                Select::make('users')
                                    ->relationship('users', 'name')
                                    ->label('Assign Users to this Role')
                                    ->options(User::pluck('name', 'id')->toArray())
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->searchPrompt('Search Users...')
                                    ->helperText('Select users who will have this role.')
                                    ->createOptionUsing(function (array $data) {
                                        $data['password'] = \Illuminate\Support\Facades\Hash::make($data['password']);
                                        $user = User::create($data);
                                        return $user->id;
                                    })
                                        ->createOptionForm([
                                        TextInput::make('name')
                                            ->label('Full name')
                                            ->required(),
                                        TextInput::make('username')
                                            ->label('Username')
                                            ->unique('users', 'username', ignoreRecord: true),
                                        TextInput::make('email')
                                            ->label('Email address')
                                            ->email()
                                            ->required()
                                            ->unique('users', 'email', ignoreRecord: true),
                                        TextInput::make('password')
                                            ->label('Password')
                                            ->revealable()
                                            ->password()
                                            ->required(),
                                        FileUpload::make('avatar_url')
                                            ->label('Avatar')
                                            ->image()
                                            ->directory('avatars')
                                            ->visibility('public')
                                            ->maxSize(102400000)
                                            ->acceptedFileTypes([
                                                'image/jpeg', 'image/png', 
                                                'image/gif', 'image/webp', 
                                                'image/svg+xml', 'application/pdf', 
                                                'video/mp4', 'video/avi', 
                                                'video/mov', 'audio/mpeg', 
                                                'audio/wav', 'text/plain',                                        
                                                'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                                            ->imageEditor(),
                                    ]),

                                TextInput::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->nullable()
                                    ->maxLength(255),

                                Select::make(config('permission.column_names.team_foreign_key'))
                                    ->label(__('filament-shield::filament-shield.field.team'))
                                    ->placeholder(__('filament-shield::filament-shield.field.team.placeholder'))
                                    /** @phpstan-ignore-next-line */
                                    ->default(Filament::getTenant()?->id)
                                    ->options(fn (): array => in_array(Utils::getTenantModel(), [null, '', '0'], true) ? [] : Utils::getTenantModel()::pluck('name', 'id')->toArray())
                                    ->visible(fn (): bool => static::shield()->isCentralApp() && Utils::isTenancyEnabled())
                                    ->dehydrated(fn (): bool => static::shield()->isCentralApp() && Utils::isTenancyEnabled()),
                                
                                static::getSelectAllFormComponent(),

                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
                static::getShieldFormComponents(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        // Return the same schema as the form for now, or customize as needed
        return static::form($schema);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->weight(FontWeight::Medium)
                    ->label(__('filament-shield::filament-shield.column.name'))
                    ->formatStateUsing(fn (string $state): string => ucwords(str_replace('_', ' ', $state)))
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('guard_name')
                    ->badge()
                    ->color('warning')
                    ->label(__('filament-shield::filament-shield.column.guard_name'))
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('team.name')
                    ->default('Global')
                    ->badge()
                    ->color(fn (mixed $state): string => str($state)->contains('Global') ? 'gray' : 'primary')
                    ->label(__('filament-shield::filament-shield.column.team'))
                    ->searchable()
                    ->visible(fn (): bool => static::shield()->isCentralApp() && Utils::isTenancyEnabled()),
                TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.permissions'))
                    ->counts('permissions')
                    ->color('primary')
                    ->alignment('center'),
                TextColumn::make('users_count')
                    ->badge()
                    ->label('Assigned Users') 
                    ->counts('users')
                    ->color('success')
                    ->alignment('center'),
                TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime()
                    ->alignment('center'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                 ViewAction::make()
                    ->modal() 
                    ->slideOver() // Optional: biar kayak drawer dari kanan (keren!)
                    ->color('info')
                    ->button()
                    ->size('lg'),
                EditAction::make()
                    ->button()
                    ->color('warning')
                    ->size('lg')
                    ->visible(fn (): bool => Gate::allows('Update:Role'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Role updated')
                            ->body('The role has been updated successfully.')
                    ),
                DeleteAction::make()
                    ->button()
                    ->color('danger')
                    ->size('lg')
                    ->visible(fn (): bool => Gate::allows('Delete:Role'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Role deleted')
                            ->body('The role has been deleted successfully.')
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->visible(fn (): bool => Gate::allows('Delete:Role')),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\Roles\RelationManagers\UsersResourceRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRoles::route('/'),
            'create' => CreateRole::route('/create'),
            // 'view' => ViewRole::route('/{record}'), // REMOVED! View pake Modal aja
            'edit' => EditRole::route('/{record}/edit'),
        ];
    }

    public static function getModel(): string
    {
        return Utils::getRoleModel();
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return Utils::getResourceSlug();
    }

    public static function getCluster(): ?string
    {
        return Utils::getResourceCluster();
    }

    public static function getEssentialsPlugin(): ?FilamentShieldPlugin
    {
        return FilamentShieldPlugin::get();
    }

    // Add this method to ensure the users relationship is loaded
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->with('users');
    }

        public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The Number Of Roles';
    }
}