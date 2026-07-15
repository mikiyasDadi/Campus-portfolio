<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_instructor_availabilities', function (Blueprint $cell) {
            // 1. Add the column as an unsigned big integer
            // 2. Set it to 'constrained' to link it to your departments table
            // 3. Use 'onDelete(cascade)' so if a department is deleted, these rules are too
            $cell->foreignId('department_id')
                 ->after('instructor_id') 
                 ->constrained('departments')
                 ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('exam_instructor_availabilities', function (Blueprint $cell) {
            // Removes the foreign key and the column if you need to rollback
            $cell->dropConstrainedForeignId('department_id');
        });
    }
};