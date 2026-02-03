<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Http\Resources\MemberResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Member::with('activeBorrowings');

        // Apply filters if present
        if ($request->has('q')) {
            $q = $request->q;

            $query->where(function ($q1) use ($q) {
                $q1->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Get members with pagination
        $members = $query->paginate(10);
        return MemberResource::collection($members);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMemberRequest $request): MemberResource|JsonResponse
    {
        try {
             $member = Member::create($request->validated());

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully create member',
                'data' => new MemberResource($member),
                'status_code' => 200
            ], 200);

             return new MemberResource($member);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create member',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): MemberResource|JsonResponse
    {
        try {
            $member = Member::with('activeBorrowings', 'borrowings')->findOrFail($id);
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully loaded a member',
                'data' => new MemberResource($member),
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Member not found',
                'error' => $e->getMessage(),
                'status_code' => 404
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreMemberRequest $request, string $id): MemberResource|JsonResponse
    {
        try {
            $member = Member::findOrFail($id);
            $member->update($request->validated());
            return response()->json([
                'status' => 'success',
                'message' => 'Successfully updated a member',
                'data' => new MemberResource($member),
                'status_code' => 200
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update member',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
       try {
            // First need to check if the member has active borrowings
            if(Member::findOrFail($id)->activeBorrowings()->count() > 0){
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cannot delete member with active borrowings',
                    'status_code' => 500
                ], 400);
            }

            $member = Member::findOrFail($id);
            $member->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Member deleted successfully',
                'status_code' => 200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete member',
                'error' => $e->getMessage(),
                'status_code' => 500
            ], 500);
        }
    }
}
