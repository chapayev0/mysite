<?php
include 'db_connect.php';
$output = "";

$res = $conn->query("SHOW CREATE TABLE paper_archive");
if ($res) {
    $row = $res->fetch_assoc();
    $output .= $row['Create Table'] . ";\n\n";
}

$res = $conn->query("SELECT * FROM paper_archive ORDER BY id ASC");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $output .= "INSERT INTO paper_archive (id, title, grade, year, paper_type, description, filepath, uploaded_by, uploaded_at) VALUES (" . 
              $row['id'] . ", '" . 
              $conn->real_escape_string($row['title']) . "', '" . 
              $conn->real_escape_string($row['grade']) . "', " . 
              ($row['year'] === null ? "NULL" : $row['year']) . ", '" . 
              $conn->real_escape_string($row['paper_type']) . "', " . 
              ($row['description'] === null ? "NULL" : "'" . $conn->real_escape_string($row['description']) . "'") . ", '" . 
              $conn->real_escape_string($row['filepath']) . "', " . 
              $row['uploaded_by'] . ", '" . 
              $conn->real_escape_string($row['uploaded_at']) . "');\n";
    }
}
file_put_contents('db_update_papers.sql', $output);
?>
