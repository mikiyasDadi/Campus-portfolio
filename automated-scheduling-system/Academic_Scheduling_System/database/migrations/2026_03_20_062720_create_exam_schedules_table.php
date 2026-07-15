<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('exam_schedules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('course_id')->constrained()->onDelete('cascade');
        
        // LIMIT LENGTHS HERE to stay under the 1000-byte index limit
        $table->string('year', 10);      // e.g., "2025/2026"
        $table->string('semester', 30);  // e.g., "First Semester"
        
        $table->integer('day_number');
        $table->string('period');
        $table->string('room_name');
        $table->unsignedBigInteger('department_id');
        $table->timestamps();
        
        // This will now fit comfortably in the index
        $table->index(['year', 'semester', 'course_id'], 'exam_idx_context');
    });
}

    public function down()
    {
        Schema::dropIfExists('exam_schedules');
    }
};