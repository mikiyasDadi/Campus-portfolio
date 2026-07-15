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
       Schema::create('instructor_profiles', function (Blueprint $table) {
    // Make user_id the Primary Key instead of a separate 'id'
    $table->unsignedBigInteger('user_id')->primary(); 
    
    $table->string('first_name');
    $table->string('last_name');
    $table->unsignedBigInteger('department_id');
    $table->string('status')->default('active');
    $table->timestamps();

    // Define the foreign key relationship
    $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_profiles');
    }
};
