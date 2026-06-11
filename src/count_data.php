<?php
include('assets/config/db.php');

$tables = [];
$res = $conn->query("SHOW TABLES");
if ($res) {
    while($row = $res->fetch_array()) {
        $tables[] = $row[0];
    }
}

$total_tables = count($tables);
$total_rows = 0;

echo "Database Summary:\n";
echo "Total Tables: $total_tables\n\n";
echo str_pad("Table Name", 30) . " | Row Count\n";
echo str_repeat("-", 45) . "\n";

foreach ($tables as $t) {
    // Avoid views if we want only tables, but COUNT(*) works on views too.
    // Let's just do COUNT(*)
    $cres = $conn->query("SELECT COUNT(*) as c FROM `$t`");
    if ($cres && $crow = $cres->fetch_assoc()) {
        $count = $crow['c'];
        $total_rows += $count;
        echo str_pad($t, 30) . " | " . $count . "\n";
    }
}

echo str_repeat("-", 45) . "\n";
echo str_pad("Total Rows Across DB", 30) . " | " . $total_rows . "\n";

?>
