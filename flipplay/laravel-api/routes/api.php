<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\UserCollectionController;

// Pencarian media
Route::get('/media/search', [MediaController::class, 'search']);

// CRUD Media
Route::apiResource('media', MediaController::class)
    ->parameters(['media' => 'media']);

// Koleksi pengguna
Route::get('/users/{user}/collection', [UserCollectionController::class, 'index']);

Route::post('/users/{user}/collection/{media}', [UserCollectionController::class, 'store']);

Route::delete('/users/{user}/collection/{media}', [UserCollectionController::class, 'destroy']);
