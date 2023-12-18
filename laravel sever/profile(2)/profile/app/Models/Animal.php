<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    use HasFactory;

    public $timestamps = true;
    protected $fillable = [
        'file_name',
        'binary_data',
    ];
}
