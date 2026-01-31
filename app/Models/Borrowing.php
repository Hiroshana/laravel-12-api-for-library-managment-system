<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrowing extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowingFactory> */
    use HasFactory;

    protected $fillable = [
        'book_id',
        'member_id',
        'borrowed_at',
        'due_at',
        'returned_at',
        'status',
    ];

    // Cast dates to Carbon instances
    protected $casts = [
        'borrowed_at' =>'date',
        'due_at' =>'date',
        'returned_at' =>'date',
    ];

    // Define relationship with Book model
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    // Define relationship with Member model
    public function member():BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    // Check if the borrowing is overdue
    public function isOverdue(): bool
    {
        return $this->due_at < Carbon::today() && $this->status === 'borrowed';
    }
}
