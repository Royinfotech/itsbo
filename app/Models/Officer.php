<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Officer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'birthdate',
        'position',
        'email',
        'contact_number',
        'image_path',
        'school_year_id'
    ];

    /**
     * Get the full name of the officer.
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function schoolYear()
    {
        return $this->belongsTo(\App\Models\SchoolYear::class);
    }
} 