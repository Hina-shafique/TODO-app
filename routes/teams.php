<?php

use App\Livewire\Teams\CreateTeam;
use App\Livewire\Teams\EditTeam;
use App\Livewire\Teams\IndexTeam;
use App\Livewire\Teams\ShowTeam;

Route::middleware('auth')->group(function () {

    Route::get('/teams', IndexTeam::class)->name('teams.index');
    Route::get('/teams/create', CreateTeam::class)->name('teams.create');
    Route::get('/teams/{team}', ShowTeam::class)->name('teams.show');
    Route::get('/teams/{team}/edit', EditTeam::class)->name('teams.edit');

});
