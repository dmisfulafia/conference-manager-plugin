<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    protected $fillable = [
        'registration_id',
        'title',
        'abstract_text',
        'abstract_file_path',
        'is_abstract_paid',
        'abstract_status',
        'abstract_rejection_reason',
        'full_paper_file_path',
        'is_full_paper_paid',
        'full_paper_status',
        'full_paper_rejection_reason',
    ];

    protected $casts = [
        'is_abstract_paid' => 'boolean',
        'is_full_paper_paid' => 'boolean',
    ];

    /**
     * Get the registration that this submission is linked to.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
