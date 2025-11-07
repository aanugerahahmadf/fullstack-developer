<?php

namespace App\Filament\Resources\Contacts\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\ContactExporter;
use Filament\Resources\Pages\ManageRecords;
use App\Filament\Resources\Contacts\ContactResource;

class ManageContacts extends ManageRecords
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make()
                //->label('Create Contact'),
            //ExportAction::make()
                //->exporter(ContactExporter::class)
                //->label('Export Contact'),
        ];
    }
}
