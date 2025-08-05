<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
    'username',
    'password',
    'role',
    'status'
];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function hasRole($role)
    {
        return strtolower($this->role) === strtolower($role);
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    public function isSecretary()
    {
        return $this->hasRole('secretary');
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('superadmin');
    }

    public function isTreasurer()
    {
        return $this->hasRole('treasurer');
    }

    public function getDashboardRoute()
    {
        try {
            return match (strtolower($this->role)) {
                'admin' => route('admin.dashboard'),
                'secretary' => route('secretary.dashboard'),
                'superadmin' => route('superadmin.dashboard'),
                'treasurer' => route('treasurer.index'),
                default => route('login'),
            };
        } catch (\Exception $e) {
            \Log::error('Dashboard route error:', [
                'user_id' => $this->id,
                'role' => $this->role,
                'error' => $e->getMessage()
            ]);
            return route('login');
        }
    }
}

