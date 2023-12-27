<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CMSLanding extends Model
{
    use HasFactory;

    protected $table = 'landing_page';

    protected $fillable = [
        'type',
        'value',
        'option',
        'is_disabled'
    ];
}
