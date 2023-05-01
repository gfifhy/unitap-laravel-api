<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'store_name',
        'store_logo',
        'user_id'
    ];
    protected $hidden = [
        'created_at',
    ];
}
