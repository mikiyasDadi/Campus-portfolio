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
       Schema::create('courses', function (Blueprint $table) {
        $table->id();
        $table->string('course_code')->unique();
        $table->string('course_name');
        $table->unsignedBigInteger('department_id'); 
        $table->integer('ects'); // 6 ECTS, 7 ECTS, etc.
        $table->string('hours'); // To store "3/2/2" format
        $table->timestamps();

        $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
