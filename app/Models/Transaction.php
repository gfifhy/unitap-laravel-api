<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'wallet_id_sender',
        'wallet_id_receiver',
        'product_id',
        'quantity',
    ];
}
