<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // The "Set" itself
        Schema::create('exam_exclusion_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('set_name')->nullable(); // Optional: "Set #1", "Irregular Students Set", etc.
            $table->timestamps();
        });

        // The courses belonging to that set
        Schema::create('exam_exclusion_group_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('exam_exclusion_groups')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_exclusion_group_courses');
        Schema::dropIfExists('exam_exclusion_groups');
    }
};