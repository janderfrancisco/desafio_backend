<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    use Traits\Uuid;

    public const STATUS_OF_TRANSACTION = [
        'pending' => 'pending',
        'completed' => 'completed',
        'canceled' => 'canceled'
    ];

    protected $fillable = [
        'payer',
        'payee',
        'value',
        'uuid',
        'was_notified',
        'was_notified_at',
        'was_reversed',
        'was_reversed_at'
    ];

    protected $casts = [
        'id'         => 'string',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
        'was_notified' => 'boolean',
        'was_reversed' => 'boolean',
        'was_reversed_at' => 'datetime:Y-m-d H:i:s',
        'was_notified_at' => 'datetime:Y-m-d H:i:s'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public function payerUser()
    {
        return $this->belongsTo(User::class, 'payer');
    }

    public function payeeUser()
    {
        return $this->belongsTo(User::class, 'payee');
    }

}
