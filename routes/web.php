<?php

use App\Livewire\Users\UserBookmarks;
use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard')
    ->middleware(['auth', 'verified', 'active'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/users/{user}/bookmarks', UserBookmarks::class)->name('users.bookmarks');

require __DIR__.'/todos.php';
require __DIR__.'/teams.php';
require __DIR__.'/projects.php';
require __DIR__.'/auth.php';
