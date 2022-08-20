<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collage extends Model
{
    use HasFactory;
    protected $table = 'collage';
    protected $primaryKey = 'coll_id';
    protected $fillable = [
        'coll_name',
        'coll_address',
        'coll_image',
        'university_id'
    ];
}
