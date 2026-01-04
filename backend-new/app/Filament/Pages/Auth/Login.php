<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Component;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Email address / Username')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login = $data['login'];
        $password = $data['password'];

        // Check if the login is an email or username
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $field => $login,
            'password' => $password,
        ];
    }
    
    protected function authenticated(): void
    {
        // Show detailed success notification
        Notification::make()
            ->title('Welcome back!')
            ->body('You have been successfully logged in to the Kilang Pertamina Internasional system at ' . now()->format('H:i:s') . '.')
            ->success()
            ->duration(5000) // Show for 5 seconds
            ->send();
            
        parent::authenticated();
    }
    
    protected function throwFailureValidationException(): never
    {
        // Show detailed error notification
        Notification::make()
            ->title('Authentication Failed')
            ->body('We couldn\'t verify your credentials. Please check your email/username and password, and try again. If you continue to have issues, contact your system administrator.')
            ->danger()
            ->duration(8000) // Show for 8 seconds
            ->send();
            
        parent::throwFailureValidationException();
    }
}