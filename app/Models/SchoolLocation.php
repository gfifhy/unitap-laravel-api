<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolLocation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'id',
        'location',
        'location_slug',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function securityGuards() {
        return $this->hasMany(SecurityGuard::class);
    }

    public function students(){
        return $this->hasMany(Student::class);
    }
}
