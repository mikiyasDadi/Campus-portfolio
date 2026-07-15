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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'year')) {
                $table->unsignedTinyInteger('year')->nullable()->after('department_id');
            }
            if (!Schema::hasColumn('users', 'section')) {
                $table->string('section', 10)->nullable()->after('year');
            }
        });

        Schema::table('official_records', function (Blueprint $table) {
            if (!Schema::hasColumn('official_records', 'year')) {
                $table->unsignedTinyInteger('year')->nullable()->after('department_id');
            }
            if (!Schema::hasColumn('official_records', 'section')) {
                $table->string('section', 10)->nullable()->after('year');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['year', 'section']);
        });

        Schema::table('official_records', function (Blueprint $table) {
            $table->dropColumn(['year', 'section']);
        });
    }
};
