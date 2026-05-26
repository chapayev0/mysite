<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

// Check if user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Disable max execution time for large databases
set_time_limit(0);

$tables = [];
$result = $conn->query("SHOW TABLES");
if ($result) {
    while ($row = $result->fetch_row()) {
        $tables[] = $row[0];
    }
}

$sqlScript = "-- Database Backup\n";
$sqlScript .= "-- Generated: " . date('Y-m-d H:i:s') . "\n\n";
$sqlScript .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($tables as $table) {
    // Add Drop Table
    $sqlScript .= "DROP TABLE IF EXISTS `$table`;\n";
    
    // Get Create Table script
    $query = "SHOW CREATE TABLE `$table`";
    $result = $conn->query($query);
    if ($result) {
        $row = $result->fetch_row();
        $sqlScript .= $row[1] . ";\n\n";
    }
    
    // Get Table Data
    $query = "SELECT * FROM `$table`";
    $result = $conn->query($query);
    if ($result) {
        $columnCount = $result->field_count;
        while ($row = $result->fetch_assoc()) {
            $sqlScript .= "INSERT INTO `$table` VALUES(";
            $values = array_values($row);
            for ($j = 0; $j < $columnCount; $j++) {
                if (isset($values[$j])) {
                    $sqlScript .= "'" . $conn->real_escape_string($values[$j]) . "'";
                } else {
                    $sqlScript .= "NULL";
                }
                if ($j < ($columnCount - 1)) {
                    $sqlScript .= ",";
                }
            }
            $sqlScript .= ");\n";
        }
    }
    $sqlScript .= "\n";
}

$sqlScript .= "SET FOREIGN_KEY_CHECKS=1;\n";

$backup_file_name = 'backup_' . date('Y-m-d_H-i-s') . '.sql';

// Send headers to download the file
header('Content-Type: application/sql');
header('Content-Disposition: attachment; filename="' . $backup_file_name . '"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');
echo $sqlScript;
exit;
?>
