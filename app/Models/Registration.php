<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Registration extends Model
{
    protected $fillable = [
        'user_id',
        'conference_id',
        'attendee_type_id',
        'wants_accommodation',
        'wants_materials',
        'is_attendance_paid',
        'is_accommodation_paid',
        'is_materials_paid',
    ];

    protected $casts = [
        'wants_accommodation' => 'boolean',
        'wants_materials' => 'boolean',
        'is_attendance_paid' => 'boolean',
        'is_accommodation_paid' => 'boolean',
        'is_materials_paid' => 'boolean',
    ];

    /**
     * Get the user who owns this registration.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the conference associated with this registration.
     */
    public function conference(): BelongsTo
    {
        return $this->belongsTo(Conference::class);
    }

    /**
     * Get the chosen attendee category pricing for this registration.
     */
    public function attendeeType(): BelongsTo
    {
        return $this->belongsTo(AttendeeType::class);
    }

    /**
     * Get the payments associated with this registration.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the submission associated with this registration.
     */
    public function submission(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Submission::class);
    }
}
