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
    Schema::create('exam_instructor_availabilities', function (Blueprint $table) {
        $table->id();
        // Tied to the instructor, not the department, for cross-dept visibility
        $table->foreignId('instructor_id')->constrained('users')->onDelete('cascade');
        $table->integer('day_number'); // Day 1, Day 2, etc.
        $table->enum('period', ['morning', 'afternoon']);
        $table->boolean('is_available')->default(true);
        $table->timestamps();
        
        // Prevent duplicate entries for the same instructor/day/period
        $table->unique(['instructor_id', 'day_number', 'period'], 'exam_avail_unique');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_instructor_availabilities');
    }
};
