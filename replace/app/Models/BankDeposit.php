<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankDeposit extends Model
{
    protected $table = 'bank_deposits';
    protected $fillable = [
        'system_collection',
        'total_deposited',
        'slip',
        'remarks'
    ];
}
