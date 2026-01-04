<?php

namespace App\Filament\Resources\Contacts;

use UnitEnum;
use BackedEnum;
use App\Models\Contact;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\Resource;
use Filament\Actions\ExportAction;
use App\Filament\Exports\ContactExporter;
use Filament\Actions\DeleteAction;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\Contacts\Pages\ManageContacts;
use Filament\Notifications\Notification;
use Filament\Actions\ViewAction;
use Illuminate\Support\Facades\Gate;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|UnitEnum|null $navigationGroup = 'CRUD For All Pages';

    protected static string|BackedEnum|null $navigationIcon = 'bxs-message-detail';

    protected static ?string $navigationLabel = 'Contact Us';

    protected static ?string $modelLabel = 'Contact Us Management';

    protected static ?string $pluralModelLabel = 'Contact Us Management';

    protected static ?int $navigationSort = 4;

    public static function canViewAny(): bool
    {
        return true; // Allow all authenticated users to view the list
    }

    public static function canCreate(): bool
    {
        return Gate::allows('Create:Contact');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('email'),
                TextInput::make('phone'),
                TextInput::make('instagram'),
                Textarea::make('address'),
            ]);
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
                TextColumn::make('email')
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('phone')
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('instagram')
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('address')
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment('center'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment('center'),
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
                    ->visible(fn (): bool => Gate::allows('Update:Contact'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Contact updated')
                            ->body('The contact has been updated successfully.')
                    ),
                DeleteAction::make()
                    ->button()
                    ->color('danger')
                    ->size('lg')
                    ->visible(fn (): bool => Gate::allows('Delete:Contact'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Contact deleted')
                            ->body('The contact has been deleted successfully.')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Gate::allows('Delete:Contact'))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Contacts deleted')
                                ->body('The selected contacts have been deleted successfully.')
                        ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageContacts::route('/'),
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
        return 'The Number Of Contacts';
    }
}