<?php

use App\Livewire\Projects\CreateProject;
use App\Livewire\Projects\EditProject;
use App\Livewire\Projects\IndexProject;
use App\Livewire\Projects\ShowProject;

Route::middleware('auth')->group(function () {

    Route::get('/teams/{team}/projects', IndexProject::class)->name('projects.index');
    Route::get('/teams/{team}/projects/create', CreateProject::class)->name('projects.create');
    Route::get('/teams/{team}/projects/{project}', ShowProject::class)->name('projects.show');
    Route::get('/teams/{team}/projects/{project}/edit', EditProject::class)->name('projects.edit');

});
