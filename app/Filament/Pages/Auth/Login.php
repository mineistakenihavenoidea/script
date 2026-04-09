<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;

class Login extends BaseLogin
{
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        TextInput::make('username')
                            ->label('Username')
                            ->required()
                            ->autofocus(),

                        TextInput::make('password')
                            ->label('Password')
                            ->password()
                            ->required(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }
}