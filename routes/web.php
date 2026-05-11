<?php

use Illuminate\Support\Facades\Route;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;

Route::view('/', 'welcome')->name('home');

Route::middleware([
    'auth',
    ValidateSessionWithWorkOS::class,
])->group(function () {
    Route::redirect('dashboard', 'tasks');
    Route::livewire('tasks', 'pages::tasks.tasks')->name('tasks');
    Route::livewire('tasks/create', 'pages::tasks.create')->name('tasks.create');
    Route::livewire('tasks/{task}', 'pages::tasks.show')->name('tasks.show');
    Route::livewire('tasks/{task}/edit', 'pages::tasks.edit')->name('tasks.edit');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
