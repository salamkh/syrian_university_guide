<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    use HasFactory;
    protected $table = 'donor';
    protected $primaryKey = 'don_id';
    protected $fillable = [
        'don_id',
        'university_id',
        'collage_id',
        'section_id',
        'class_id'
    ];
}
