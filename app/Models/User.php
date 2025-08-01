<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role', // ✅ This must be included
        'email_verified_at',
        'password',
        'remember_token',
    ];
    public function collegePayment()
    {
        return $this->hasMany(Payment::class, 'processed_by');
    }
    public function shsPayment()
    {
        return $this->hasMany(ShsPayment::class, 'processed_by');
    }
    public function otherPayment()
    {
        return $this->hasMany(otherPayment::class, 'processed_by');
    }
    public function uniformPayment()
    {
        return $this->hasMany(UniformPayment::class, 'processed_by');
    }
    public function oldAccountPayment()
    {
        return $this->hasMany(OldAccPayment::class, 'processed_by');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
