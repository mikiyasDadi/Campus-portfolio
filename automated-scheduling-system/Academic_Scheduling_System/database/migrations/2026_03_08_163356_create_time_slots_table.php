<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('time_slots', function (Blueprint $table) {
        $table->id();
        $table->time('start_time');
        $table->time('end_time');
        // Optional: you can add a 'type' if you want to distinguish between 'lecture' and 'lab' slots
        $table->string('type')->default('lecture'); 
        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('time_slots');
}
};
