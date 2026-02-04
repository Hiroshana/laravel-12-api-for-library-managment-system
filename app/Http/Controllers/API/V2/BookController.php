<?php

namespace App\Http\Controllers\API\V2;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;

class BookController extends Controller
{
       /**
     * For V2 API - Additional functionalities can be added here
     */
    public function get_latest_five_books(Request $request): JsonResponse
    {
        try {
            $books = Book::latest()->with('author')->take(5)->get();

            return response()->json([
                'status' => 'success',
                'message' => 'This is a sample function for API version 2',
                'data' => BookResource::collection($books),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the latest books.',
                'error' => $e->getMessage()
            ], 500);

        }
    }
}
