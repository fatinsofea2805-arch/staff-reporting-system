<?php
require_once __DIR__ . '/database.php';
date_default_timezone_set('Asia/Kuala_Lumpur');

if (!extension_loaded('pdo_sqlite')) {
    http_response_code(500);
    exit('Enable the pdo_sqlite extension in php.ini, then restart PHP. See README.md.');
}

// A test/deployment can point to a separate database without changing this file.
$databasePath = getenv('STAFF_DB_PATH') ?: __DIR__ . '/data/ptjpm.sqlite';
if (!is_file($databasePath)) {
    http_response_code(500);
    exit('SQLite database missing. Restore data/ptjpm.sqlite from the project ZIP or your backup.');
}
try {
    $dbCon = new PDO('sqlite:' . $databasePath, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    $dbCon->exec('PRAGMA busy_timeout = 5000');
} catch (PDOException $error) {
    error_log($error->getMessage());
    http_response_code(500);
    exit('Could not open SQLite. Check that the database file and data folder are writable.');
}
