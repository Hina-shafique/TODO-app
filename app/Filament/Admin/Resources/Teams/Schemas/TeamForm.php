<?php

namespace App\Filament\Admin\Resources\Teams\Schemas;

use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('description')
                    ->nullable()
                    ->rows(3)
                    ->maxLength(2000),
                Select::make('owner_id')
                    ->label('Owner')
                    ->options(User::pluck('name', 'id'))
                    ->required()
                    ->searchable(),
            ]);
    }
}
