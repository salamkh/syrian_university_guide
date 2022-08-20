<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectClass extends Model
{
    use HasFactory;
    protected $table = 'subject_classs';
    protected $primaryKey = 'subject_id';
    protected $fillable = [
            'subject_name',
            'domain_id',
            'practical_availablity',
            'theoritical_availablity'
        ];
}
