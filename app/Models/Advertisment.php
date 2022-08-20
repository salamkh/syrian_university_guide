<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisment extends Model
{
    use HasFactory;
    protected $table = 'advertisment';
    protected $primaryKey = 'advertisment_id';
    protected $fillable = [
        'advertisment_id',
        'collage_id',
        'content',
        'publish_date'
    ];
}
