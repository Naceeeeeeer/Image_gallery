<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pallette extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'file_name',
    ];}
