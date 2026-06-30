<?php

namespace App\Filament\Admin\Resources\Projects\Schemas;

use App\Enum\ProjectStatus;
use App\Models\Team;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('team_id')
                    ->label('Team')
                    ->options(Team::pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->nullable()
                    ->rows(3)
                    ->maxLength(2000),
                Select::make('status')
                    ->options(collect(ProjectStatus::cases())->mapWithKeys(
                        fn (ProjectStatus $s) => [$s->value => $s->label()]
                    ))
                    ->required()
                    ->default(ProjectStatus::ACTIVE->value),
                DatePicker::make('due_date')
                    ->nullable(),
            ]);
    }
}
