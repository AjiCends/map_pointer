<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'name',
        'latitude',
        'longitude',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }
}
