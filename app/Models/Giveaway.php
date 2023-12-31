<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Giveaway extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
