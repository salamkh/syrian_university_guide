<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherCetification extends Model
{
    use HasFactory;
    protected $table = 'teacher_certification';
    protected $primaryKey = 'id';
    protected $fillable = [
        'teacher_id',
        'degree',
        'description',
        'date'
    ];
}
