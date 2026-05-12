<?php
// Simple import script for the workspace `spk_topsis.sql` dump.
// USAGE: open in browser and click confirm link: http://localhost/spk-topsis/scripts/import_sql.php

require_once __DIR__ . '/../config/database.php';

// Restrict invocation to localhost for safety (allow typical localhost hostnames)
$remote = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
$host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? '');
$allowedLocal = ['127.0.0.1', '::1'];
if (php_sapi_name() !== 'cli' && !in_array($remote, $allowedLocal, true) && stripos($host, 'localhost') === false) {
    echo "Access denied: import may only be run from localhost.";
    exit;
}

if (php_sapi_name() === 'cli') {
    echo "Run this from the browser: http://localhost/spk-topsis/scripts/import_sql.php\n";
    exit;
}

if (!file_exists(__DIR__ . '/../spk_topsis.sql')) {
    echo "Error: spk_topsis.sql not found in project root.";
    exit;
}

if (!isset($_GET['confirm']) || $_GET['confirm'] !== '1') {
    echo '<h2>Import spk_topsis.sql</h2>';
    echo '<p>This will execute SQL statements from <strong>spk_topsis.sql</strong> against the database configured in <code>config/database.php</code>.</p>';
    echo '<p>Make sure your MySQL is running and credentials in <code>config/database.php</code> are correct.</p>';
    echo '<p><a href="?confirm=1">Click here to run the import</a></p>';
    exit;
}

$sql = file_get_contents(__DIR__ . '/../spk_topsis.sql');
// Remove Windows CR for safety
$sql = str_replace("\r", "", $sql);
// Remove CHECK(...) clauses which may not be supported on older MySQL versions
$sql = preg_replace('/CHECK\s*\([^\)]*\)/i', '', $sql);
// Remove database create/use commands so we run statements against configured DB
$sql = preg_replace('/CREATE\s+DATABASE[^;]*;?/i', '', $sql);
$sql = preg_replace('/USE\s+[^;]*;?/i', '', $sql);

// Split statements on semicolon followed by newline or end-of-string (safer)
$chunks = preg_split('/;\s*(?=\n|$)/', $sql);
$errors = [];
$executed = 0;
// disable FK checks once for the import
try {
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
} catch (Exception $e) {
    $errors[] = 'Could not disable FK checks: ' . $e->getMessage();
}
foreach ($chunks as $chunk) {
    $stmt = trim($chunk);
    if ($stmt === '') continue;
    // remove leading comments lines
    $stmt = preg_replace('/(^--.*$\n?)+/m', '', $stmt);
    $stmt = preg_replace('/(^#.*$\n?)+/m', '', $stmt);
    $stmt = trim($stmt);
    if ($stmt === '') continue;
    // skip DELIMITER tokens
    if (preg_match('/^DELIMITER\s+/i', $stmt)) continue;
    try {
        $pdo->exec($stmt);
        $executed++;
    } catch (PDOException $e) {
        $errors[] = $e->getMessage() . ' -- SQL: ' . (strlen($stmt) > 200 ? substr($stmt,0,200).'...' : $stmt);
    }
}
// re-enable FK checks
try { $pdo->exec('SET FOREIGN_KEY_CHECKS=1'); } catch (Exception $ex) { $errors[] = 'Could not re-enable FK checks: ' . $ex->getMessage(); }

echo '<h2>Import completed</h2>';
if (!empty($errors)) {
    echo '<div style="background:#fee2e2;padding:10px;border-radius:6px;color:#8b0000;"><strong>Errors:</strong><ul>';
    foreach ($errors as $err) {
        echo '<li>' . htmlspecialchars($err) . '</li>';
    }
    echo '</ul></div>';
} else {
    echo '<p style="color:green">No errors detected. Database should be imported.</p>';
}

echo '<p><a href="/spk-topsis/index.php">Go to app</a></p>';

?>