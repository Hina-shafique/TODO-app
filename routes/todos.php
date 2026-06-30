<?php

use App\Livewire\Todos\CreateTodo;
use App\Livewire\Todos\EditTodo;
use App\Livewire\Todos\IndexTodo;
use App\Livewire\Todos\ShowTodo;

Route::middleware('auth')->group(function () {
    Route::get('/todos', IndexTodo::class)->name('todos.index');
    Route::get('todo/create', CreateTodo::class)->name('todos.create');
    Route::get('todo/{todo}', ShowTodo::class)->name('todos.show');
    Route::get('todo/{todo}/edit', EditTodo::class)->name('todos.edit');
});
