<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Borrowing;
use Illuminate\Http\Request;
use App\Http\Resources\BorrowingResource;
use App\Http\Requests\StoreBorrowingRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BorrowingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index():AnonymousResourceCollection
    {
        $query = Borrowing::with(['member', 'book']);

        if (request()->has('status')) {
            $query->where('status', request()->status);
        }

        if (request()->has('member_id')) {
            $query->where('member_id', request()->member_id);
        }
        if (request()->has('book_id')) {
            $query->where('book_id', request()->book_id);
        }

        $borrowings = $query->paginate(10);
        return BorrowingResource::collection($borrowings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBorrowingRequest $request): JsonResponse
    {
       try {

            // Check if the book has available copies
            $book = Book::findOrFail($request->book_id);
            if (!$book->isAvailable()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No available copies for this book',
                    'status_code' => 400
                ], 400);
            }

            // Create the borrowing record
            $borrowing = Borrowing::create($request->validated());

            // Decrease the available copies of the book
            $book->borrow();

            //Load relations
            $borrowing->load(['member', 'book']);

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully created borrowing record',
                'data' => new BorrowingResource($borrowing),
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create borrowing record',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     * @return JsonResponse
     * @param string $id
     */
    public function show(string $id): JsonResponse
    {
        try {
            $borrowing = Borrowing::with(['member', 'book'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully loaded a borrowing record',
                'data' => new BorrowingResource($borrowing),
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Borrowing record not found',
                'error' => $e->getMessage(),
                'status_code' => 404
            ], 404);
        }
    }

    /**
     * Return borrowing book the specified resource in storage.
     * @return JsonResponse
     * @param Request $request
     * @param string $id : Borrowing ID
     */
    public function return_book(Request $request, string $id):JsonResponse
    {
        try {

            $borrowing = Borrowing::findOrFail($id);

            info($borrowing);

            // Check if borrowing exists
            if ($borrowing->status ==='returned') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Borrowing record not found',
                    'status_code' => 404
                ], 404);
            }

            // Update the borrowing record
            $borrowing->update([
                'returned_at' => now(),
                'status' => 'returned',
            ]);

            // Increase the available copies of the book
            $borrowing->book->returnBook();

            //Load relations
            $borrowing->load(['member', 'book']);

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully updated borrowing record',
                'data' => new BorrowingResource($borrowing),
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update borrowing record',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }


      /**
     * Update overdue status after check after checking date.
     */
    public function overdue(Request $request):JsonResponse
    {
        try {

           $overdue = Borrowing::where('status', 'borrowed')->where('due_at', '<', now())->get();

            // Update the borrowing overdue record
            Borrowing::where('status', 'borrowed')
            ->where('due_at', '<', now())
            ->update(['status' => 'overdue']);

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully marked borrowing record as overdue',
                'data' => BorrowingResource::collection( $overdue ),
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to marked borrowing record as overdue',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}
