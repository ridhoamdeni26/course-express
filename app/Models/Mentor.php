<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mentor extends Model
{
    use HasFactory;

    //arahin ke table tujuan
    protected $table = 'mentors';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
    ];

    // yang ingin di isi
    protected $fillable = [
        'name', 'profile', 'email', 'profession'
    ];
}
