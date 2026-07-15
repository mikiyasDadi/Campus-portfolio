<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    try {
        $count = DB::table($tableName)->count();
        echo "$tableName: $count\n";
    } catch (\Exception $e) {
        echo "$tableName: Error - " . $e->getMessage() . "\n";
    }
}