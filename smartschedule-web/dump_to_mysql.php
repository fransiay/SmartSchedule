<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
$sql = "";

foreach ($tables as $table) {
    // Skip migration tables
    if ($table === 'migrations' || $table === 'sqlite_sequence') continue;

    $rows = DB::table($table)->get();
    if ($rows->isEmpty()) continue;

    $sql .= "-- Data for table `$table`\n";
    foreach ($rows as $row) {
        $rowArray = (array) $row;
        $columns = array_keys($rowArray);
        $values = array_values($rowArray);

        $escapedValues = array_map(function($val) {
            if (is_null($val)) return 'NULL';
            if (is_numeric($val)) return $val;
            return "'" . addslashes($val) . "'";
        }, $values);

        $colsString = "`" . implode("`, `", $columns) . "`";
        $valsString = implode(", ", $escapedValues);

        $sql .= "INSERT INTO `$table` ($colsString) VALUES ($valsString);\n";
    }
    $sql .= "\n";
}

file_put_contents(__DIR__.'/mysql_data_dump.sql', $sql);
echo "Dump generated successfully at mysql_data_dump.sql\n";
