<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;
    protected $table = 'certification';
    protected $primaryKey = 'cer_id';
    protected $fillable = [
        'cer_id',
        'cer_type',
        'donor_id'
    ];
}
