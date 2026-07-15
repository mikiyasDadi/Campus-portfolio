<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    if (!Schema::hasTable('schedules')) {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('course_code');
            $table->unsignedBigInteger('instructor_id'); 
            $table->string('type');      
            $table->string('day');       
            $table->integer('period');   
            $table->integer('year');     
            $table->integer('semester'); 
            
            // ADD THIS LINE:
            $table->string('status')->default('published'); // or 'draft'
            
            $table->timestamps();
            $table->foreign('instructor_id')->references('id')->on('users');
        });
    }
}

public function down(): void
{
    Schema::dropIfExists('schedules');
}
};
