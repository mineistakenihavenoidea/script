<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;

class Login extends BaseLogin
{
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    protected function attemptLogin(array $data): bool
    {
        return auth()->attempt(
            [
                'username' => $data['username'],
                'password' => $data['password'],
            ],
            $data['remember'] ?? false
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->autofocus(),

                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(),

                Checkbox::make('remember')
                    ->label('Remember me'),
            ]);
    }
}