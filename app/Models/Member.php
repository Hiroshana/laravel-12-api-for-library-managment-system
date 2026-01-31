<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    /** @use HasFactory<\Database\Factories\MemberFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'membership_date',
        'status',
    ];

    // Cast dates to Carbon instances
    protected $casts = [
        'membership_date' => 'date',
    ];

    // Define relationship with Borrowing model
    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    // Check if the Active Borrowings
    public function activeBorrowings(): HasMany
    {
        return $this->borrowings()->where('status', 'borrowed');
    }
}
