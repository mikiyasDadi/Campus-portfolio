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
    Schema::table('users', function (Blueprint $table) {
        // Add new columns after username
        $table->string('first_name')->after('username')->nullable();
        $table->string('last_name')->after('first_name')->nullable();
    });

    // Optional: If you have existing data, you can run a quick query here 
    // to split the full_name string before dropping the column.

    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('full_name');
    });
}

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('full_name')->after('username');
        $table->dropColumn(['first_name', 'last_name']);
    });
}
};
