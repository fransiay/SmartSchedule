<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (App\Models\User::all() as $u) {
    echo "User: " . $u->email . " (Tasks: " . $u->tasks()->count() . ", Avail: ". $u->availabilities()->count() . ")\n";
    $sch = $u->schedules()->get();
    echo "  Schedules: " . $sch->count() . "\n";
    if ($sch->count() > 0) {
        $first = $sch->first();
        echo "  First start: " . $first->start_time . "\n";
    }
}
