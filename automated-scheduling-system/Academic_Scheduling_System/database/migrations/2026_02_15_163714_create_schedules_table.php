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
        Schema::create('schedules', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('department_id'); // Missing Column 1
        $table->string('status')->default('draft');  // Missing Column 2
        // Add other fields you might need later
        $table->string('academic_year')->nullable();
        $table->string('semester')->nullable();
        $table->timestamps();

        // Foreign key to ensure data integrity
        $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
