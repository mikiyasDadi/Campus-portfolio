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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            
            // 1. We replace head_name with user_id to link to the users table
            $table->unsignedBigInteger('user_id')->nullable();
            
            $table->integer('instructor_count')->default(0);
            $table->integer('course_count')->default(0);
            $table->timestamps();

            // 2. We define the Foreign Key relationship
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null'); // Keeps the dept if the user is deleted
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};