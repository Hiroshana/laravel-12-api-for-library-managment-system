<?php

use App\Models\Book;
use App\Models\Member;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\BorrowingController;

// Authentication Routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // User Routes
    Route::get('/auth/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    //Author, Book, Member Routes
    Route::apiResource('authors', AuthorController::class);
    Route::apiResource('books', BookController::class);
    Route::apiResource('members', MemberController::class);

    // Borrowing Routes
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
});




