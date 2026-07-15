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
        Schema::create('faculties', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique(); // e.g., Faculty of Informatics
        $table->string('code')->unique(); // e.g., FOI
        $table->timestamps();
    });

    // Add faculty_id to users table
    Schema::table('users', function (Blueprint $table) {
        $table->foreignId('faculty_id')->nullable()->constrained('faculties')->onDelete('set null');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
