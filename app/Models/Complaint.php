<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    protected $fillable = [
        'user_id',
        'subject',
        'message',
        'status',
        'admin_reply',
    ];

    /**
     * Get the user who submitted this complaint.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
