<?php
include 'db_connect.php';

// Create or find Grade 10 category
$cat_id = 0;
$cat_res = $conn->query("SELECT id FROM playground_categories WHERE grade_level = '10' AND is_grade_based = 1");
if ($cat_res && $cat_res->num_rows > 0) {
    $cat_id = $cat_res->fetch_assoc()['id'];
} else {
    $conn->query("INSERT INTO playground_categories (name, icon, is_grade_based, grade_level, display_order) VALUES ('Grade 10 Logic', 'fa-microchip', 1, '10', 10)");
    $cat_id = $conn->insert_id;
}

// Add the game
$title = 'AND Gate Logic Simulator';
$desc = 'Interactive AND gate simulator. Explore all combinations in the truth table.';
$path = 'assest/games/and_gate_simulator.html';

// Check if game exists
$game_check = $conn->prepare("SELECT id FROM playground_games WHERE game_file_path = ?");
$game_check->bind_param("s", $path);
$game_check->execute();
$game_res = $game_check->get_result();

if ($game_res && $game_res->num_rows > 0) {
    echo "Game already exists.\n";
} else {
    $stmt = $conn->prepare("INSERT INTO playground_games (title, description, game_file_path, game_type, difficulty_level, recommended_age) VALUES (?, ?, ?, 'html', 'medium', 15)");
    $stmt->bind_param("sss", $title, $desc, $path);
    if ($stmt->execute()) {
        $game_id = $conn->insert_id;
        // Map to category
        $conn->query("INSERT INTO playground_game_categories (game_id, category_id) VALUES ($game_id, $cat_id)");
        echo "Game added successfully!\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
}
?>
