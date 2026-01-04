<?php

namespace App\Filament\Resources\Buildings;

use App\Filament\Resources\Buildings\Pages\ManageBuildings;
use App\Models\Building;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\BuildingExporter;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;

class BuildingResource extends Resource
{
    protected static ?string $model = Building::class;

    protected static string|UnitEnum|null $navigationGroup = 'CRUD For All Pages';
    
    protected static string|BackedEnum|null $navigationIcon = 'govicon-building';
    
    protected static ?string $navigationLabel = 'Building';

    protected static ?string $modelLabel = 'Building Management';

    protected static ?string $pluralModelLabel = 'Building Management';

    protected static ?int $navigationSort = 1;

    public static function canViewAny(): bool
    {
        return true; // Allow all authenticated users to view the list
    }

    public static function canCreate(): bool
    {
        // Removed explicit Gate check to let policies/Gate interceptor handle it
        return parent::canCreate();
    }
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('latitude')
                    ->label('Latitude Cordinate')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state !== null && is_numeric($state)) {
                            $dms = \App\Utils\CoordinateConverter::decimalToDMS(floatval($state), true);
                            // Could store DMS conversion if needed
                        }
                    }),
                TextInput::make('longitude')
                    ->label('Longitude Cordinate')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state !== null && is_numeric($state)) {
                            $dms = \App\Utils\CoordinateConverter::decimalToDMS(floatval($state), false);
                            // Could store DMS conversion if needed
                        }
                    }),
                TextInput::make('marker_icon_url')
                    ->label('Marker Icon')
                    ->placeholder('https://blade-ui-kit.com/blade-icons/govicon-building')
                    ->default('https://blade-ui-kit.com/blade-icons/govicon-building'),
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
                TextColumn::make('name')
                    ->searchable()
                    ->alignment('center'),
                TextColumn::make('latitude')
                    ->label('Latitude')
                    ->searchable()
                    ->toggleable()
                    ->alignment('center')
                    ->formatStateUsing(fn ($state): string => \App\Utils\CoordinateConverter::formatCoordinate($state, 8))
                    ->tooltip(function ($state, $record): string {
                        if (!$state) return 'No latitude coordinate';
                        $dms = \App\Utils\CoordinateConverter::decimalToDMS(floatval($state), true);
                        return "Decimal: {$state}°\nDMS: {$dms}\nAccuracy: ±1.1mm";
                    }),
                TextColumn::make('longitude')
                    ->label('Longitude')
                    ->searchable()
                    ->toggleable()
                    ->alignment('center')
                    ->formatStateUsing(fn ($state): string => \App\Utils\CoordinateConverter::formatCoordinate($state, 8))
                    ->tooltip(function ($state, $record): string {
                        if (!$state) return 'No longitude coordinate';
                        $dms = \App\Utils\CoordinateConverter::decimalToDMS(floatval($state), false);
                        return "Decimal: {$state}°\nDMS: {$dms}\nAccuracy: ±1.1mm";
                    }),
                TextColumn::make('marker_icon_url')
                    ->searchable()
                    ->toggleable()
                    ->formatStateUsing(fn ($state): string => $state ?? 'Using default icon')
                    ->url(fn ($record) => $record->marker_icon_url)
                    ->openUrlInNewTab()
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
                    ->visible(fn (): bool => Gate::allows('Update:Building'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Building updated')
                            ->body('The building has been updated successfully.')
                    ),
                DeleteAction::make()
                    ->button()
                    ->color('danger')
                    ->size('lg')
                    ->visible(fn (): bool => Gate::allows('Delete:Building'))
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Building deleted')
                            ->body('The building has been deleted successfully.')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn (): bool => Gate::allows('Delete:Building'))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title('Buildings deleted')
                                ->body('The selected buildings have been deleted successfully.')
                        ),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBuildings::route('/'),
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
        return 'The Number Of Building';
    }
}