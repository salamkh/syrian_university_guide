<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    use HasFactory;
    protected $table = 'class';
    protected $primaryKey = 'class_id';
    protected $fillable = [
        'class_id',
        'class_name',
        'section_id'
    ];
}
