<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Notifications\Notification;
use App\Filament\Exports\RoleExporter;
use Illuminate\Support\Facades\Gate;
use App\Filament\Exports\Roles\RoleExporter as RolesRoleExporter;
use Filament\Resources\Pages\ListRecords;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => Gate::allows('Create:Role'))   
                ->label('Create Role')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Role created')
                        ->body('The role has been created successfully.')
                ),
            ExportAction::make()
                ->exporter(RoleExporter::class)
                ->label('Export Role'),
        ];
    }
}