<?php
include 'db_connect.php';

$tables = [
    'paper_archive',
    'playground_categories',
    'playground_game_categories',
    'playground_games',
    'wall_posts',
    'messages'
];

$output = "-- Database update script for new features\n\n";

foreach ($tables as $table) {
    $res = $conn->query("SHOW CREATE TABLE " . $table);
    if ($res) {
        $row = $res->fetch_assoc();
        // Replace CREATE TABLE with CREATE TABLE IF NOT EXISTS
        $sql = str_replace("CREATE TABLE", "CREATE TABLE IF NOT EXISTS", $row['Create Table']);
        $output .= "-- Table: $table\n";
        $output .= $sql . ";\n\n";
    }
}

file_put_contents('db_update_new_features.sql', $output);
echo "Done";
?>
