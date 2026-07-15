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
    Schema::table('department_instructor', function (Blueprint $table) {
        // Remove the old column if it exists
        if (Schema::hasColumn('department_instructor', 'instructor_profile_id')) {
            $table->dropForeign(['instructor_profile_id']);
            $table->dropColumn('instructor_profile_id');
        }
        // Add the correct user_id column
        if (!Schema::hasColumn('department_instructor', 'user_id')) {
            $table->foreignId('user_id')->after('department_id')->constrained()->onDelete('cascade');
        }
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
