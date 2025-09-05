<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id',
        'image_url',
    ];

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}
