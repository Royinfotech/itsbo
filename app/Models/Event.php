<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events'; // Define the table name explicitly

    protected $primaryKey = 'event_id'; // Set the primary key

    public $incrementing = true; // Specify if the primary key is auto-incrementing

    protected $fillable = [
        'event_name',
        'event_location',
        'event_date',
        'time_duration', // 'whole_day', 'half_day_morning', 'half_day_afternoon'
        'open_scan_type',
        'is_finished',
        'school_year_id' // Add this
    ];

    protected $appends = ['available_scan_types'];

    protected $casts = [
        'event_date' => 'datetime',
        'available_scan_types' => 'array'
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'event_id', 'event_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id');
    }

    public function scopeCurrentSchoolYear($query)
    {
        $currentSchoolYear = SchoolYear::where('is_open', true)->first();
        return $query->where('school_year_id', $currentSchoolYear?->id);
    }

    public function getAvailableScanTypesAttribute()
    {
        switch ($this->time_duration) {
            case 'Whole Day':
                return [
                    'morning' => ['am_in', 'am_out'],
                    'afternoon' => ['pm_in', 'pm_out']
                ];
            case 'Half Day: Morning':
                return [
                    'morning' => ['am_in', 'am_out'],
                    'afternoon' => []
                ];
            case 'Half Day: Afternoon':
                return [
                    'morning' => [],
                    'afternoon' => ['pm_in', 'pm_out']
                ];
            default:
                return [
                    'morning' => [],
                    'afternoon' => []
                ];
        }
    }
}

