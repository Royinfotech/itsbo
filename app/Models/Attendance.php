<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';
    protected $primaryKey = 'attendance_id';

    protected $fillable = [
        'event_id',
        'student_id',
        'attendance_date',
        'am_in',
        'am_in_time',
        'am_out',
        'am_out_time',
        'pm_in',
        'pm_in_time',
        'pm_out',
        'pm_out_time',
        'school_year_id' // Add this
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'am_in' => 'boolean',
        'am_out' => 'boolean',
        'pm_in' => 'boolean',
        'pm_out' => 'boolean',
        'am_in_time' => 'datetime',
        'am_out_time' => 'datetime',
        'pm_in_time' => 'datetime',
        'pm_out_time' => 'datetime'
    ];

    // Helper method to get attendance status
    public function getStatusAttribute()
    {
        if ($this->am_in && $this->pm_in) {
            return 'Present';
        } elseif (!$this->am_in && !$this->pm_in) {
            return 'Absent';
        }
    }

    // Helper method to format time
    public function getFormattedTime($time)
    {
        return $time ? Carbon::parse($time)->format('h:i A') : '-';
    }

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'student_id');
    }

    // Add school year relationship
    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class, 'school_year_id');
    }

    
   
    // Add scope for current school year
    public function scopeCurrentSchoolYear($query)
    {
        $currentSchoolYear = SchoolYear::where('is_open', true)->first();
        return $query->where('school_year_id', $currentSchoolYear?->id);
    }
}
