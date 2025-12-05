<?php

namespace App\Filament\Resources\Contacts\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\ContactExporter;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;
use App\Filament\Resources\Contacts\ContactResource;

class ManageContacts extends ManageRecords
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
                CreateAction::make()
                    ->visible(fn (): bool => Gate::allows('Create:Contact'))
                    ->label('Create Contact')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Contact created')
                            ->body('The contact has been created successfully.')
                    ),
                ExportAction::make()
                    ->visible(fn (): bool => Gate::allows('Export:Contact'))
                    ->exporter(ContactExporter::class)
                    ->label('Export Contact'),
        ];
    }
}