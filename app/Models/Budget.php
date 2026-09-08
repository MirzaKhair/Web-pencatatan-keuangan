<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Budget extends Model
{
    protected $fillable = ['user_id', 'category_id', 'amount', 'month', 'year'];

    protected $casts = [
        'amount' => 'decimal:2',
        'month' => 'integer',
        'year' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getUsedAttribute(): float
    {
        return (float) $this->user->transactions()
            ->where('category_id', $this->category_id)
            ->whereMonth('transaction_date', $this->month)
            ->whereYear('transaction_date', $this->year)
            ->sum('transactions.amount');
    }

    public function getRemainingAttribute(): float
    {
        return (float) $this->amount - $this->used;
    }

    public function getProgressAttribute(): float
    {
        return $this->amount > 0 ? ($this->used / $this->amount) * 100 : 0;
    }

    public function getStatusAttribute(): string
    {
        $progress = $this->progress;

        if ($progress >= 100) {
            return 'Melebihi';
        } elseif ($progress >= 80) {
            return 'Hampir Batas';
        }

        return 'Aman';
    }
}
