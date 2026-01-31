<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Book extends Model
{
    /** @use HasFactory<\Database\Factories\BookFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'isbn',
        'description',
        'author_id',
        'genre',
        'published_at',
        'total_copies',
        'available_copies',
        'cover_image',
        'price',
        'status'
    ];

    // Define relationship with Author model
    public function author():BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function borrowings():HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    // Check if the book is available
    public function isAvailable():bool
    {
        return $this->available_copies > 0;
    }

    // Decrease available copies by 1 when a book is borrowed
    public function borrow():bool
    {
        if ($this->isAvailable()) {
            $this->decrement('available_copies');
            return true;
        }
        return false;
    }
    // Increase available copies by 1 when a book is returned
    public function returnBook():void
    {
        $this->increment('available_copies');
    }
}



