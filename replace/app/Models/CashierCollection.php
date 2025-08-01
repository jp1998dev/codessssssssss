<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashierCollection extends Model
{
    protected $table = 'cashier_collections';
    protected $fillable = [
        'cashier_id',
        'system_collection',
        'actual_collection',
        'variance',
        'note'
    ];

    public function cashier(){
        return $this->belongsTo(User::class,'cashier_id');
    }
}
