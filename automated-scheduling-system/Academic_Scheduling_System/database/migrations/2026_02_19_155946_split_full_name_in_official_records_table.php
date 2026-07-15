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
    Schema::table('official_records', function (Blueprint $table) {
        $table->string('first_name')->after('id');
        $table->string('last_name')->after('first_name');
        $table->dropColumn('full_name');
    });
}

public function down()
{
    Schema::table('official_records', function (Blueprint $table) {
        $table->string('full_name');
        $table->dropColumn(['first_name', 'last_name']);
    });
}
};
