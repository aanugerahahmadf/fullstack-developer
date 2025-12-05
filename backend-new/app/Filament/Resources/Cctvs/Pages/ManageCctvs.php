<?php

namespace App\Filament\Resources\Cctvs\Pages;

use App\Filament\Resources\Cctvs\Widgets\DashboardStatOverview;
use App\Filament\Widgets\DashboardStat;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use App\Filament\Exports\CctvExporter;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;
use App\Filament\Resources\Cctvs\CctvResource;
use Illuminate\Support\Facades\Gate;


class ManageCctvs extends ManageRecords
{
    protected static string $resource = CctvResource::class;

    protected function getHeaderActions(): array
    {
        return [
                CreateAction::make()
                    ->visible(fn (): bool => Gate::allows('Create:Cctv'))
                    ->label('Create Cctv')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('CCTV created')
                            ->body('The CCTV has been created successfully.')
                    ),
                ExportAction::make()
                    ->visible(fn (): bool => Gate::allows('Export:Cctv'))
                    ->exporter(CctvExporter::class)
                    ->label('Export Cctv'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardStatOverview::class,
        ];
    }

}
