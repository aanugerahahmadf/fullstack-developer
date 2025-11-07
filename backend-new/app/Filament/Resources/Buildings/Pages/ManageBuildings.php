<?php

namespace App\Filament\Resources\Buildings\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\BuildingExporter;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Buildings\BuildingResource;

class ManageBuildings extends ManageRecords
{
    protected static string $resource = BuildingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make()
                //->label('Create Building'),
            //ExportAction::make()
                //->exporter(BuildingExporter::class)
                //->label('Export Building'),
        ];
    }
}
