<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_instructor_availabilities', function (Blueprint $table) {
            // 1. Drop the old unique index that is causing the error
            // Need to drop foreign key first if it's using this index
            if (Schema::hasColumn('exam_instructor_availabilities', 'instructor_id')) {
                $table->dropForeign(['instructor_id']);
            }
            
            $table->dropUnique('exam_avail_unique');

            // Restore foreign key
            $table->foreign('instructor_id')->references('id')->on('users')->onDelete('cascade');

            // 2. Create a new unique index that includes department_id
            // This allows Dept A and Dept B to both have a record for the same instructor/day/period
            $table->unique(['instructor_id', 'day_number', 'period', 'department_id'], 'exam_avail_dept_unique');
        });
    }

    public function down(): void
    {
        Schema::table('exam_instructor_availabilities', function (Blueprint $table) {
            $table->dropUnique('exam_avail_dept_unique');
            $table->unique(['instructor_id', 'day_number', 'period'], 'exam_avail_unique');
        });
    }
};