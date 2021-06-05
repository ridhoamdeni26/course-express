<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyCourse extends Model
{
    use HasFactory;

    //arahin ke table tujuan
    protected $table = 'my_courses';

    // yang ingin di isi
    protected $fillable = [
        'course_id', 'user_id'
    ];
    
    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s',
    ];

    public function course()
    {
        return $this->belongsTo('App\Models\Course');
    }
}
