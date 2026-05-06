-- Create Utilities category if it doesn't exist
INSERT INTO tool_categories (name, icon, display_order, status)
SELECT 'Utilities', 'fa-wrench', 2, 'active'
WHERE NOT EXISTS (SELECT 1 FROM tool_categories WHERE name = 'Utilities');

-- Insert the Glassmorphism Generator tool
INSERT INTO tools (title, description, tool_url, category_id, is_playground)
SELECT 'CSS Glassmorphism Generator', 'Create beautiful, frosted glass UI effects instantly with real-time visual editing.', 'glassmorphism_generator.php', id, 0
FROM tool_categories 
WHERE name = 'Utilities'
LIMIT 1;
