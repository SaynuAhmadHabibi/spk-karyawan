<?php
require_once __DIR__ . '/../config/database.php';
function tableExists($pdo, $table){
    try{
        $stmt = $pdo->query("SELECT 1 FROM $table LIMIT 1");
        return $stmt !== false;
    } catch (PDOException $e){ return false; }
}
$tables = ['karyawan','kriteria','penilaian','hasil_topsis'];
echo "<h2>DB Check for spk_topsis</h2>";
foreach($tables as $t){
    $exists = tableExists($pdo, $t) ? 'YES' : 'NO';
    echo "<p><strong>$t</strong>: $exists</p>\n";
    if ($exists){
        try{
            $count = $pdo->query("SELECT COUNT(*) as c FROM $t")->fetch()['c'];
            echo "<p>Count: $count</p>\n";
            echo "<pre>Sample rows:\n";
            $rows = $pdo->query("SELECT * FROM $t LIMIT 5")->fetchAll();
            echo htmlspecialchars(print_r($rows, true));
            echo "</pre>\n";
        } catch (PDOException $e){
            echo "<p style='color:red'>Error reading $t: " . htmlspecialchars($e->getMessage()) . "</p>\n";
        }
    }
}
echo '<p><a href="/spk-topsis/index.php">Back to app</a></p>';
?>