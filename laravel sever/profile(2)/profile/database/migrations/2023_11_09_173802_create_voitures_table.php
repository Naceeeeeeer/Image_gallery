<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::create('voitures', function (Blueprint $table) {
            $table->id();
            $table->string('file_name');
            $table->binary('binary_data'); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('voitures');
    }
};