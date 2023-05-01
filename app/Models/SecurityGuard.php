<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityGuard extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = [
        'id',
        'user_id',
        'location_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function location() {
        return $this->belongsTo(SchoolLocation::class, 'location_id');
    }

}
