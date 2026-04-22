<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Task;
use App\Models\Schedule;

echo "--- Database Status ---\n";
try {
    echo "Users: " . User::count() . "\n";
    foreach(User::all() as $u) {
        echo " - " . $u->id . ": " . $u->email . " (" . $u->name . ")\n";
    }

    echo "Tasks: " . Task::count() . "\n";
    echo "Schedules: " . Schedule::count() . "\n";

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "--- End ---\n";
