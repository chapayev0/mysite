-- ==========================================================
-- LIVE DB UPDATE SCRIPT
-- Contains: Tools Feature & Logic Gate Simulator Update
-- Safety: Uses IF NOT EXISTS and IGNORE to prevent data loss
-- ==========================================================

-- 1. Create tool_categories table
CREATE TABLE IF NOT EXISTS `tool_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Create tools table
CREATE TABLE IF NOT EXISTS `tools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `tool_url` varchar(500) NOT NULL,
  `category_id` int(11) NOT NULL,
  `is_playground` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `tools_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `tool_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Insert 'Tools' link into the navigation menu
INSERT INTO menu_links (name, url, position, is_custom, is_visible, order_index) 
SELECT 'Tools', 'tools.php', 'navbar', 1, 1, 6
WHERE NOT EXISTS (SELECT 1 FROM menu_links WHERE url = 'tools.php');

-- 4. Set up default Games category in Tools Hub
INSERT INTO tool_categories (name, icon, display_order) 
SELECT 'Games', 'fa-gamepad', 1
WHERE NOT EXISTS (SELECT 1 FROM tool_categories WHERE name = 'Games');

-- 5. Insert Playground tile into Tools Hub
INSERT INTO tools (title, description, tool_url, category_id, is_playground) 
SELECT 'Playground', 'Explore fun and educational games.', 'playground.php', id, 1
FROM tool_categories 
WHERE name = 'Games'
AND NOT EXISTS (SELECT 1 FROM tools WHERE title = 'Playground');

-- ==========================================================
-- LOGIC GATE SIMULATOR (Playground DB Integration)
-- ==========================================================

-- 6. Ensure Grade 10 Logic category exists in Playground
INSERT INTO playground_categories (name, icon, is_grade_based, grade_level, display_order)
SELECT 'Grade 10 Logic', 'fa-microchip', 1, '10', 10
WHERE NOT EXISTS (SELECT 1 FROM playground_categories WHERE name = 'Grade 10 Logic' AND is_grade_based = 1);

-- 7. Insert the Logic Gate Simulator game
INSERT INTO playground_games (title, description, game_file_path, game_type, difficulty_level, recommended_age)
SELECT 'Universal Logic Gate Simulator', 'Interactive simulator for AND, OR, NOT, NAND, NOR, XOR, XNOR gates.', 'assest/games/logic_gate_simulator.html', 'html', 'medium', 15
WHERE NOT EXISTS (SELECT 1 FROM playground_games WHERE game_file_path = 'assest/games/logic_gate_simulator.html');

-- 8. Map the Game to the Category
INSERT INTO playground_game_categories (game_id, category_id)
SELECT g.id, c.id 
FROM playground_games g
JOIN playground_categories c ON c.name = 'Grade 10 Logic'
WHERE g.game_file_path = 'assest/games/logic_gate_simulator.html'
AND NOT EXISTS (
    SELECT 1 FROM playground_game_categories pgc 
    WHERE pgc.game_id = g.id AND pgc.category_id = c.id
);
