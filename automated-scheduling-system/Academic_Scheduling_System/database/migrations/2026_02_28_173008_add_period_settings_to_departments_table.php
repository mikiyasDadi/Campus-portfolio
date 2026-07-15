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
       Schema::table('departments', function (Blueprint $table) {
        $table->integer('class_duration')->default(50)->comment('In minutes');
        $table->integer('lab_duration')->default(100)->comment('In minutes');
        $table->integer('total_periods')->default(8)->comment('Periods per day');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            //
        });
    }
};
