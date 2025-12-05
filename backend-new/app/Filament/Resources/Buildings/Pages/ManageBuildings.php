<?php

namespace App\Filament\Resources\Buildings\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\BuildingExporter;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;
use App\Filament\Resources\Buildings\BuildingResource;
use Illuminate\Support\Facades\Gate;

class ManageBuildings extends ManageRecords
{
    protected static string $resource = BuildingResource::class;

    protected function getHeaderActions(): array
    {
        return [
                CreateAction::make()
                    ->visible(fn (): bool => Gate::allows('Create:Building'))
                    ->label('Create Building')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Building created')
                            ->body('The building has been created successfully.')
                    ),
                ExportAction::make()
                    ->visible(fn (): bool => Gate::allows('Export:Building'))
                    ->exporter(BuildingExporter::class)
                    ->label('Export Building'),
        ];
    }
}
