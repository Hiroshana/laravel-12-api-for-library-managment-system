<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('authors', AuthorController::class);
Route::apiResource('books', App\Http\Controllers\BookController::class);
Route::apiResource('members', App\Http\Controllers\MemberController::class);
Route::apiResource('borrowings', App\Http\Controllers\BorrowingController::class);
