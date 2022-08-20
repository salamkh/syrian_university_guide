<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectDomian extends Model
{
    use HasFactory;
    protected $table = 'knowledg_domain';
    protected $primaryKey = 'domain_id';
    protected $fillable = [
        'domain_name',
    ];
}
