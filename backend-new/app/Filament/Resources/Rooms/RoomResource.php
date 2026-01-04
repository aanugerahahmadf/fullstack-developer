<?php

namespace App\Filament\Resources\Rooms;

use UnitEnum;
use BackedEnum;
use App\Models\Room;
use App\Models\Building;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\RoomExporter;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\Rooms\Pages\ManageRooms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static string|UnitEnum|null $navigationGroup = 'CRUD For All Pages';

    protected static string|BackedEnum|null $navigationIcon = 'gmdi-meeting-room';

    protected static ?string $navigationLabel = 'Room';

    protected static ?string $modelLabel = 'Room Management';

    protected static ?string $pluralModelLabel = 'Room Management';

    protected static ?int $navigationSort = 2;


    public static function canViewAny(): bool
    {
        return true; // Allow all authenticated users to view the list
    }

    public static function canCreate(): bool
    {
        return Gate::allows('Create:Room');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('building_id')
                    ->label('Building')
                    ->options(Building::pluck('name', 'id')->unique())
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->searchPrompt('Search Building...')
                    ->required()
                    ->live(),
                TextInput::make('name')
                    ->required(),
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
                TextColumn::make('building.name')
                    ->label('Name Building')
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('name')
                    ->label('Name Room')
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
                    ->visible(fn (): bool => Gate::allows('Update:Room'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Room updated')
                            ->body('The room has been updated successfully.')
                    ),
                DeleteAction::make()
                    ->button()
                    ->color('danger')
                    ->size('lg')
                    ->visible(fn (): bool => Gate::allows('Delete:Room'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Room deleted')
                            ->body('The room has been deleted successfully.')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Gate::allows('Delete:Room'))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Rooms deleted')
                                ->body('The selected rooms have been deleted successfully.')
                        ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRooms::route('/'),
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
        return 'The Number Of Room';
    }
}