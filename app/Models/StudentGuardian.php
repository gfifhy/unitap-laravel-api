<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGuardian extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'contact',
    ];
}
