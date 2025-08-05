<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentFor extends Model
{
    protected $table = 'payment_fors';
    
    protected $fillable = [
        'name',
        'amount',
        'school_year_id',
    ];
    

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    // Scope for current school year
    public function scopeCurrentSchoolYear($query)
    {
        return $query->whereHas('schoolYear', function($q) {
            $q->where('is_open', true);
        });
    }
}
