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
       Schema::table('courses', function (Blueprint $table) {
        $table->integer('year')->after('course_name')->nullable();
        $table->integer('semester')->after('year')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('courses', function (Blueprint $table) {
        $table->dropColumn(['year', 'semester']);
    });
    }
};
