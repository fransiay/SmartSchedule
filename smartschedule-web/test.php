<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('email', 'test@example.com')->first();

$tasks = $user->tasks()->where('status', '!=', 'done')->get();
echo "Total tasks to schedule: " . count($tasks) . "\n";
foreach($tasks as $t) {
    echo "ID: " . $t->id . " Status: " . $t->status . " Duration: " . $t->duration_minutes . " Deadline: " . $t->deadline . "\n";
}

$svc = new App\Services\SmartScheduleService();
$schedules = $svc->generateForUser($user);
echo "Generated " . count($schedules) . " schedules.\n";
