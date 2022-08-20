<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheoriticalPortion extends Model
{
    use HasFactory;
    protected $table = 'theoritical_portion';
    protected $primaryKey = 'id';
    protected $fillable = [
        'portion',
        'teacher_id',
        'subject_id'
    ];
}
