<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Looking for guided/query tables ===\n\n";

$tables = \DB::select('SHOW TABLES');
foreach($tables as $t) {
    $name = array_values((array)$t)[0];
    if(stripos($name, 'guide') !== false || stripos($name, 'query') !== false) {
        echo "Found table: $name\n";
        
        // Show structure
        $columns = \DB::select("DESCRIBE $name");
        echo "  Columns: ";
        $cols = [];
        foreach($columns as $col) {
            $cols[] = $col->Field;
        }
        echo implode(', ', $cols) . "\n";
        
        // Show count
        $count = \DB::table($name)->count();
        echo "  Row count: $count\n\n";
    }
}
