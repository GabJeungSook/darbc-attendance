<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
