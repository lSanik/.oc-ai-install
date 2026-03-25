<?php
die('');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ============================================================
$config = [
    'host'     => 'localhost',
    'port'     => 3306,
    'database' => 'your_database',
    'username' => 'your_username',
    'password' => 'your_password',
];
// ============================================================

$mysqli = new mysqli(
    $config['host'],
    $config['username'],
    $config['password'],
    $config['database'],
    $config['port']
);

if ($mysqli->connect_error) {
    die("Connection error: " . $mysqli->connect_error . "\n");
}

$mysqli->set_charset('utf8mb4');

// ---- збір таблиць ----
$tablesResult = $mysqli->query("SHOW TABLES");
$tables = [];
while ($row = $tablesResult->fetch_array()) {
    $tables[] = $row[0];
}
$total = count($tables);

// ---- папки ----
$mapDir    = __DIR__ . '/map';
$tablesDir = $mapDir . '/db_tables';

if (!is_dir($mapDir))    mkdir($mapDir, 0755, true);
if (!is_dir($tablesDir)) mkdir($tablesDir, 0755, true);

// ---- db_map.php (індекс) ----
$indexLines = [];
$indexLines[] = '<?php';
$indexLines[] = '';
$indexLines[] = '$db_map = "DB: ' . $config['database'] . ' | Generated: ' . date('Y-m-d H:i:s') . ' | Tables: ' . $total . '\n';
foreach ($tables as $table) {
    $indexLines[] = $table . '\n';
}
$indexLines[] = '";';
$indexLines[] = '';

file_put_contents($mapDir . '/db_map.php', implode("\n", $indexLines));

// ---- окремі файли таблиць ----
foreach ($tables as $table) {
    $createResult = $mysqli->query("SHOW CREATE TABLE `{$table}`");
    $createRow    = $createResult->fetch_assoc();
    $ddl          = $createRow['Create Table'] . ";";

    // екрануємо для PHP рядка
    $ddlEscaped = str_replace(
        ['\\', '"'],
        ['\\\\', '\\"'],
        $ddl
    );

    $content  = '<?php' . "\n";
    $content .= '' . "\n";
    $content .= '$ddl = "' . $ddlEscaped . '";' . "\n";

    file_put_contents($tablesDir . '/' . $table . '.php', $content);
}

$mysqli->close();

echo "Done. Generated " . $total . " tables in map/\n";
echo "Index: map/db_map.php\n";
echo "Tables: map/db_tables/\n";
