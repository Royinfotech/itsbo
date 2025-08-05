<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'payment_for_id',
        'amount',
        'or_number',
        'payment_date',
        'school_year_id'
    ];

    protected $casts = [
        'payment_date' => 'datetime',
        'amount' => 'decimal:2'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function paymentFor()
    {
        return $this->belongsTo(PaymentFor::class, 'payment_for_id');
    }

    public function schoolYear()
    {
        return $this->belongsTo(SchoolYear::class);
    }

}