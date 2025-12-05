<?php

namespace App\Filament\Resources\Roles\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\Users\UserResource;

class UsersResourceRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    public function form(Schema $schema): Schema
    {
        return UserResource::form($schema);
    }

    public function infolist(Schema $schema): Schema
    {
        return UserResource::infolist($schema);
    }

    public function table(Table $table): Table
    {
        return UserResource::table($table);
    }
}