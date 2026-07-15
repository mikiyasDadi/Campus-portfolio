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
        Schema::create('official_records', function (Blueprint $table) {
        $table->id();
        $table->string('id_number')->unique(); // The Numerical ID (e.g., 12345)
        $table->string('email')->unique();
        $table->string('full_name');
        $table->unsignedTinyInteger('role_id'); // 1:Admin, 2:Faculty Head, 3:DH, 4:Instructor, 5:Student
        $table->unsignedBigInteger('department_id')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('official_records');
    }
};
