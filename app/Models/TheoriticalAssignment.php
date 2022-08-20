<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TheoriticalAssignment extends Model
{
    use HasFactory;
    protected $table = 'theoritical_assignment';
    protected $primaryKey = 'id';
    protected $fillable = [
        'type',
        'value',
        'subject_id'
    ];
}
