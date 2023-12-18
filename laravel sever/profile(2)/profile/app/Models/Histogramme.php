<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Histogramme extends Model
{

        protected $table = 'histogramme'; // Assuming your table name is 'texts'
        public $timestamps = false;
        protected $fillable = [
            'file_name',
        ];
    }
