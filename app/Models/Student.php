<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory, HasUuids;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'nfc_id',
        'first_name',
        'last_name',
        'student_id',
        'wallet_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'deleted_at'
    ];

}
