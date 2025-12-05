<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\UserExporter;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Gate;
use Filament\Resources\Pages\ManageRecords;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                    ->visible(fn (): bool => Gate::allows('Create:User'))
                    ->label('Create User')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('User created')
                            ->body('The user has been created successfully.')
                    ),
            ExportAction::make()
                ->visible(fn (): bool => Gate::allows('Export:User'))
                ->exporter(UserExporter::class)
                ->label('Export User'),
        ];
    }
}