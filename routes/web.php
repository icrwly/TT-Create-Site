<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\RootDirController;
use App\Http\Controllers\PantheonController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/create-site', [SiteController::class, 'showForm'])->name('create.site.form');
Route::post('/create-site', [SiteController::class, 'create'])->name('site.create'); // Ensure this matches your form action
//Route::get('/root-directory', [RootDirController::class, 'showRootDirectory']);
//Route::get('/pantheon', [PantheonController::class, 'listSites']);