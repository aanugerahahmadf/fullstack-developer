<?php

namespace App\Livewire;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Components\Component as SchemaComponent;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Joaopaulolndev\FilamentEditProfile\Concerns\HasSort;

class UsernameComponent extends Component implements HasSchemas
{
    use InteractsWithSchemas;
    use HasSort;

    public ?array $data = [];

    protected static int $sort = 10;

    public function mount(): void
    {
        $user = Auth::user();
        $this->form->fill([
            'username' => $user->username,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        $user = Auth::user();
        
        return $schema
            ->statePath('data')
            ->components([
                Section::make('Username')
                    ->aside()
                    ->description('Update your username')
                    ->schema([
                        TextInput::make('username')
                            ->label('Username')
                            ->placeholder('Enter your username')
                            ->required()
                            ->minLength(3)
                            ->maxLength(255)
                            ->unique(table: 'users', column: 'username', ignorable: $user)
                            ->autocomplete('username')
                            ->columnSpan('full'),
                    ]),
            ]);
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();
            
            Log::info('UsernameComponent: Saving username', ['data' => $data]);
            
            $user = Auth::user();
            $user->username = $data['username'];
            $user->save();
            
            Log::info('UsernameComponent: Username saved successfully', ['user_id' => $user->id, 'username' => $user->username]);
            
            $this->dispatch('profile-updated');
        } catch (\Exception $e) {
            Log::error('UsernameComponent: Error saving username', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    public function render(): View
    {
        return view('livewire.username-component');
    }
}