<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::first();
echo "Username: " . $user->username . "\n";
echo "Status: " . $user->status . "\n";
$attempt = Auth::attempt(['username' => $user->username, 'password' => 'password', 'status' => 'active']);
echo "Login result: " . ($attempt ? "true" : "false") . "\n";