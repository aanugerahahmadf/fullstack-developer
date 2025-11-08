<?php

namespace App\Filament\Resources\Contacts;

use UnitEnum;
use BackedEnum;
use App\Models\Contact;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|UnitEnum|null $navigationGroup = 'Contact Us';

    protected static ?string $navigationLabel = 'Contact';

    protected static ?string $modelLabel = 'Contact Us';

    protected static ?string $pluralModelLabel = 'Contact Us';

    protected static ?int $navigationSort = 1;

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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment('center'),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignment('center'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Create Contact'),
                ExportAction::make()
                    ->exporter(ContactExporter::class)
                    ->label('Export Contact'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->button()
                    ->color('info')
                    ->size('lg'),
                EditAction::make()
                    ->button()
                    ->color('warning')
                    ->size('lg'),
                DeleteAction::make()
                    ->button()
                    ->color('danger')
                    ->size('lg'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
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
