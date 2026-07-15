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
    Schema::create('instructor_availabilities', function (Blueprint $table) {
    $table->id();
    // This now references the primary key (user_id) of instructor_profiles
    $table->unsignedBigInteger('instructor_profile_id'); 
    $table->integer('day_of_week');
    $table->integer('time_slot_id');
    $table->string('type')->default('manual');
    $table->unsignedBigInteger('department_id');
    $table->timestamps();

    $table->foreign('instructor_profile_id')
          ->references('user_id') // Points to the new PK
          ->on('instructor_profiles')
          ->onDelete('cascade');
});
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_availabilities');
    }
};
