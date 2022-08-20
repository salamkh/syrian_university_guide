<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectExperience extends Model
{
    use HasFactory;
    protected $table = 'subject_experience';
    protected $primaryKey = 'id';
    protected $fillable = [
        'subject_id',
        'exp_years'
    ];
}
