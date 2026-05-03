<?php
include 'db_connect.php';
$res = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $res->fetch_array()) {
    $tables[] = $row[0];
}
echo implode(", ", $tables);
?>
