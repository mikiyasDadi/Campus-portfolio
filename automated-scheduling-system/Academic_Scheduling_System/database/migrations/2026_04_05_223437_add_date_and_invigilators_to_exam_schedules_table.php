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
        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->date('exam_date')->nullable()->after('period');
            $table->unsignedBigInteger('inv1_id')->nullable()->after('room_name');
            $table->unsignedBigInteger('inv2_id')->nullable()->after('inv1_id');
            $table->string('inv1_name')->nullable()->after('inv2_id');
            $table->string('inv2_name')->nullable()->after('inv1_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->dropColumn(['exam_date', 'inv1_id', 'inv2_id', 'inv1_name', 'inv2_name']);
        });
    }
};
