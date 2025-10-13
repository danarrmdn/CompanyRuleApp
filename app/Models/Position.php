<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_title',
        'holder_id',
    ];

    public function holder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'holder_id');
    }
}
