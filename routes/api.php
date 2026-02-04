<?php

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BorrowingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('authors', AuthorController::class);
Route::apiResource('books', BookController::class);
Route::apiResource('members', MemberController::class);

Route::apiResource('borrowings', BorrowingController::class)->only(['index', 'store', 'show']);
Route::post('/borrowings/{borrowing}/return-book', [BorrowingController::class, 'return_book']);
Route::get('/borrowings/overdue/list', [BorrowingController::class, 'overdue']);

// Statistics Routes
Route::get('/statistics', function () {
    $totalBooks = Book::count();
    $totalMembers = Member::count();
    $totalBorrowings = Borrowing::where('status', 'borrowed')->count();
    $overdueBorrowings = Borrowing::where('status', 'overdue')->count();

    return response()->json([
        'total_books' => $totalBooks,
        'total_members' => $totalMembers,
        'total_borrowings' => $totalBorrowings,
        'overdue_borrowings' => $overdueBorrowings,
    ]);
});

