<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkExperiance extends Model
{
    use HasFactory;
    protected $table = 'work_experiance';
    protected $primaryKey = 'exp_id';
    protected $fillable = [
        'teacher_id',
        'subject_id',
        'duration'
    ];
}
