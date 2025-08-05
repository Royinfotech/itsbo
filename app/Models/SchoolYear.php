<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $fillable = [
        'year',
        'semester',
        'is_open',
        'officer_limit',
        'open_positions',
        'positions_locked',
        'opened_by',
        'opened_at',
        'closed_at'
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'positions_locked' => 'boolean',
        'open_positions' => 'array',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime'
    ];

    // Relationship with officers
    public function officers()
    {
        return $this->hasMany(Officer::class);
    }

    // Relationship with user who opened the school year
    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    // Helper method to check if positions can be modified
    public function canModifyPositions()
    {
        return !$this->positions_locked;
    }

    public function getBasicYearAttribute()
    {
        // Returns just the year part without semester
        return $this->year;
    }

    public function getPreviousSemesterAttribute()
    {
        return SchoolYear::where('year', $this->year)
                        ->where('semester', '!=', $this->semester)
                        ->orderBy('created_at', 'desc')
                        ->first();
    }

    public function canOpenNewSemester()
    {
        // Check if this is first semester and second semester doesn't exist yet
        if ($this->semester === '1st') {
            return !SchoolYear::where('year', $this->year)
                             ->where('semester', '2nd')
                             ->exists();
        }
        return false;
    }
}