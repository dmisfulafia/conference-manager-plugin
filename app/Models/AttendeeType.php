<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendeeType extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'conference_id',
        'name',
        'fee',
    ];

    /**
     * Get the conference associated with this attendee type.
     */
    public function conference()
    {
        return $this->belongsTo(Conference::class);
    }
}
