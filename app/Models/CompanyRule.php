<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyRule extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'status',
        'category',
        'number',
        'document_name',
        'reason_of_revision',
        'effective_date',
        'file_path',
        'creator_id',
        'controller_1_id',
        'controller_2_id',
        'controller_3_id',
        'controller_4_id',
        'controller_5_id',
        'approver_1_id',
        'approver_2_id',
        'approver_3_id',
        'sent_back_by_id',
        'reason',
        'version',
        'is_obsolete',
        'previous_version_id',
        // 'file_path_temp' DIHAPUS DARI SINI
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function controller1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'controller_1_id');
    }

    public function controller2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'controller_2_id');
    }

    public function controller3(): BelongsTo
    {
        return $this->belongsTo(User::class, 'controller_3_id');
    }

    public function controller4(): BelongsTo
    {
        return $this->belongsTo(User::class, 'controller_4_id');
    }

    public function controller5(): BelongsTo
    {
        return $this->belongsTo(User::class, 'controller_5_id');
    }

    public function approver1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_1_id');
    }

    public function approver2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_2_id');
    }

    public function approver3(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_3_id');
    }

    public function logs()
    {
        return $this->hasMany(CompanyRuleLog::class)->latest();
    }

    public function logActivity($activity, $details = null)
    {
        $this->logs()->create([
            'user_id' => auth()->user()->id,
            'activity' => $activity,
            'details' => $details,
        ]);
    }
}
