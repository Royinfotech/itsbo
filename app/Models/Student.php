<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $fillable = [
        'student_name',
        'student_id',
        'year_level',
        'birthdate',
        'age',
        'birthplace',
        'email',
        'username',
        'password',
        'photo',
        'status'
    ];

    protected $hidden = [
        'password'
    ];

    protected $dates = [
        'birthdate',
        'created_at',
        'updated_at'
    ];

      // Relationship with Attendance model
    public function attendanceRecords()
    {
        return $this->hasMany(Attendance::class, 'student_id', 'student_id');
    }
    // Check if student is active
    public function isActive()
    {
        return $this->status === 'active';
    }

    // Relationship with Payment model
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }
    
}