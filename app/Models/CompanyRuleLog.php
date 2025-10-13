<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyRuleLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_rule_id',
        'user_id',
        'activity',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function rule()
    {
        return $this->belongsTo(CompanyRule::class, 'company_rule_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
