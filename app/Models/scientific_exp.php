<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class scientific_exp extends Model
{
    use HasFactory;
    protected $table = 'scientific_experience';
    protected $primaryKey = 'id';
    protected $fillable = [
        'description',
        'teacher_id',
    ];
}
