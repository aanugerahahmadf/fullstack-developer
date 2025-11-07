<?php

namespace App\Filament\Resources\Cctvs;

use UnitEnum;
use BackedEnum;
use App\Models\Cctv;
use App\Models\Building;
use App\Models\Room;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\CctvExporter;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\BulkActionGroup;
use Filament\Forms\Components\Select;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\Cctvs\Pages\ManageCctvs;
use Filament\Actions\CreateAction;

class CctvResource extends Resource
{
    protected static ?string $model = Cctv::class;

    protected static string|UnitEnum|null $navigationGroup = 'Playlist And Maps';

    protected static ?string $navigationLabel = 'Cctv';

    protected static ?string $modelLabel = 'Cctv Management';

    protected static ?string $pluralModelLabel = 'Cctv Management';

    protected static ?int $navigationSort = 3;

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
                Select::make('room_id')
                    ->label('Room')
                    ->options(Room::pluck('name', 'id')->unique())
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->searchPrompt('Search Room...')
                    ->required()
                    ->live(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('username')
                    ->default('admin'),
                TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->default('password.123'),
                TextInput::make('ip_address')
                    ->required()
                    ->ipv4(),
                TextInput::make('ip_rtsp_url')
                    ->label('RTSP URL')
                    ->url()
                    ->placeholder('rtsp://username:password@ip:port/path'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('building.name')
                    ->searchable(),
                TextColumn::make('room.name')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->searchable(),
                TextColumn::make('ip_rtsp_url')
                    ->label('RTSP URL')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Create Cctv'),
                ExportAction::make()
                    ->exporter(CctvExporter::class)
                    ->label('Export Cctv'),
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
            'index' => ManageCctvs::route('/'),
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
        return 'The Number Of CCTV';
    }

}
