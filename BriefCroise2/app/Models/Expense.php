<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'colocation_id',
        'payer_id',
        'category_id',
        'title',
        'amount',
        'date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date'   => 'date',
    ];

    public function colocation(): BelongsTo
    {
        return $this->belongsTo(Colocation::class);
    }

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}