<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conference extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'address',
        'next_of_kin',
        'next_of_kin_phone',
        'status',
        'venue',
        'accommodation_fee',
        'conference_material_fee',
        'abstract_fee',
        'full_paper_fee',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'accommodation_fee' => 'decimal:2',
            'conference_material_fee' => 'decimal:2',
            'abstract_fee' => 'decimal:2',
            'full_paper_fee' => 'decimal:2',
        ];
    }

    /**
     * Get the attendee pricing categories for this conference.
     */
    public function attendeeTypes()
    {
        return $this->hasMany(AttendeeType::class);
    }
}
