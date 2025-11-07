<?php

namespace App\Filament\Resources\Cctvs\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\CctvExporter;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Cctvs\CctvResource;

class ManageCctvs extends ManageRecords
{
    protected static string $resource = CctvResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make()
                //->label('Create Cctv'),
            //ExportAction::make()
                //->exporter(CctvExporter::class)
                //->label('Export Cctv'),
        ];
    }
}
