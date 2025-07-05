<?php

use Illuminate\Support\Facades\Route;

// Todo管理画面のルート
Route::get('/', function () {
    return view('todos.index');
})->name('todos.index');
