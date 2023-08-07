<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'document',
        'type',
        'wallet',
        'password',
    ];

    public const TYPE_OF_USER = [
        'client' => 'client',
        'seller' => 'seller'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function transactions()
    {
        if ($this->type === self::TYPE_OF_USER['client']) 
            return $this->hasMany(Transaction::class, 'payer_id');
        else if ($this->type === self::TYPE_OF_USER['seller'])
            return $this->hasMany(Transaction::class, 'payee_id');

    }
}
