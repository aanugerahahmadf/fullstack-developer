<?php

namespace App\Filament\Resources\Users;

use UnitEnum;
use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Actions\ViewAction;
use Illuminate\Support\Facades\Gate;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|UnitEnum|null $navigationGroup = 'Roles User';

    protected static string|BackedEnum|null $navigationIcon = 'bxs-user-account';

    protected static ?string $navigationLabel = 'User';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'User Management';

    protected static ?int $navigationSort = 1;

    public static function canCreate(): bool
    {
        return Gate::allows('Create:User');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Full name')
                    ->required(),
                TextInput::make('username')
                    ->label('Username'),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
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
                    ->maxSize(1024)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif'])
                    ->imageEditor(),
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
                TextColumn::make('position')
                    ->label('ID')
                    ->getStateUsing(function ($record, $rowLoop) {
                        return $rowLoop->iteration;
                    })
                    ->alignment('center'),
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->alignment('center'),
                ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->imageHeight(40)
                    ->circular()
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('username')
                    ->label('Username')
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('password')
                    ->label('Password')
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('created_at')
                    ->label('Created at')
                    ->alignment('center')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->alignment('center')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
                    ->button()
                    ->color('info')
                    ->size('lg'),
                EditAction::make()
                    ->button()
                    ->color('warning')
                    ->size('lg')
                    ->visible(fn (): bool => Gate::allows('Update:User'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('User updated')
                            ->body('The user has been updated successfully.')
                    ),
                DeleteAction::make()
                    ->button()
                    ->color('danger')
                    ->size('lg')
                    ->visible(fn (): bool => Gate::allows('Delete:User'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('User deleted')
                            ->body('The user has been deleted successfully.')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Gate::allows('Delete:User')),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }

        public static function getNavigationBadge(): ?string
    {
        return static::$model::count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'The Number Of User';
    }
}