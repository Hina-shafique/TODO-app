<?php

use App\Livewire\Todos\CreateTodo;
use App\Livewire\Todos\IndexTodo;
use Illuminate\Support\Facades\Route;
use App\Livewire\Todos\EditTodo;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'active'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');
    
Route::middleware('auth')->group(function () {
    Route::get('/todos', IndexTodo::class)->name('todos.index');
    Route::get('todo/create', CreateTodo::class)->name('todos.create');
    Route::get('todo/{todo}/edit', EditTodo::class)->name('todos.edit');
});

require __DIR__ . '/auth.php';
