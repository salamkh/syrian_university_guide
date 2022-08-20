<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PracticalAssignment extends Model
{
    use HasFactory;
    protected $table = 'practical_assigmnent';
    protected $primaryKey = 'id';
    protected $fillable = [
        'type',
        'value',
        'subject_id'
    ];
}
