<?php

namespace App\Filament\Resources\Rooms\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\RoomExporter;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;
use App\Filament\Resources\Rooms\RoomResource;

class ManageRooms extends ManageRecords
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
                CreateAction::make()
                    ->visible(fn (): bool => Gate::allows('Create:Room'))
                    ->label('Create Room')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Room created')
                            ->body('The room has been created successfully.')
                    ),
                ExportAction::make()
                    ->visible(fn (): bool => Gate::allows('Export:Room'))
                    ->exporter(RoomExporter::class)
                    ->label('Export Room'),
        ];
    }
}