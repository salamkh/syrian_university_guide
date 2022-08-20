<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class theoritical_portion_assignment extends Model
{
    use HasFactory;
    protected $table = 'theoritical_portion_assignment';
    protected $primaryKey = 'id';
    protected $fillable = [
        'portion_id',
        'assignment_id'
    ];
}
