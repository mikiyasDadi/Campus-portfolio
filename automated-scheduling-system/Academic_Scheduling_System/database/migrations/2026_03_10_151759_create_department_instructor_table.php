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
    Schema::create('department_instructor', function (Blueprint $table) {
        $table->id();
        $table->foreignId('department_id')->constrained()->onDelete('cascade');
        // Reverted to original because it was failing a later migration
        $table->unsignedBigInteger('instructor_profile_id');
        $table->foreign('instructor_profile_id')->references('user_id')->on('instructor_profiles')->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_instructor');
    }
};
