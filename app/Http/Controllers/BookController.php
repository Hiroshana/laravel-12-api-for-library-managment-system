<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\JsonResponse;
use PhpParser\Node\Stmt\TryCatch;


class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Book::with('author');

        // Apply filters if present
       if($request->has('search')){
            $search = $request->search;

            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhereHas('author', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if($request->has('genre')){
            $query->where('genre', $request->genre);
        }
        
        $books = $query->paginate(10);
        return BookResource::collection($books);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request): BookResource
    {
        $book = Book::create($request->validated());
        $book->load('author');
        return new BookResource($book);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id ): BookResource|JsonResponse
    {
        try {
            $book = Book::findOrFail($id);
            $book->load('author');
            return new BookResource($book);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the book details.',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreBookRequest $request, string $id):BookResource|JsonResponse
    {
        try {
            $book = Book::findOrFail($id);
            $book->update($request->all());
            $book->load('author');
            return new BookResource($book);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the book.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $book = Book::findOrFail($id);
            $book->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Book deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the book.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
