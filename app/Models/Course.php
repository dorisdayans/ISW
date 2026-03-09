<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'code',
        'course_name',
        'status',
        'capacity',
        'start_date',
        'start_time',
        // OJO: enrolled_count y available_seats NO van aquí (los controla el sistema)
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'capacity' => 'integer',
            'enrolled_count' => 'integer',
            'available_seats' => 'integer',
            'start_date' => 'date',
            'start_time' => 'string', // time suele manejarse como string en PHP    
        ];
    }

    /*
        ¿Dónde está la FK?
            aquí → belongsTo
            allá → hasMany / hasOne
    */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'course_id', 'id');
    }
    
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id');
    }
}
