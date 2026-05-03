-- 1. Create Menu Links Table
CREATE TABLE `menu_links` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `position` enum('navbar','footer_quick_links','footer_classes') NOT NULL,
  `is_custom` tinyint(1) DEFAULT 0,
  `is_visible` tinyint(1) DEFAULT 1,
  `order_index` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `menu_links_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `menu_links` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Insert Default Menu Links
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (1, NULL, 'Home', 'index.php#home', 'navbar', 0, 1, 1);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (2, NULL, 'Courses ▾', '#', 'navbar', 0, 1, 2);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (3, 2, 'Robotics Course', 'reg/index.html', 'navbar', 0, 1, 1);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (4, NULL, 'Classes', 'index.php#classes', 'navbar', 0, 1, 3);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (5, NULL, 'Online Classes', 'index.php#online', 'navbar', 0, 1, 4);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (6, NULL, 'Store', 'index.php#store', 'navbar', 0, 1, 5);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (7, NULL, 'Playground', 'playground.php', 'navbar', 0, 0, 6);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (8, NULL, 'Wall of Talent', 'wall.php', 'navbar', 0, 0, 7);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (9, NULL, 'About', 'about.php', 'navbar', 0, 1, 8);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (10, NULL, 'Home', '#home', 'footer_quick_links', 0, 1, 1);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (11, NULL, 'Classes', '#classes', 'footer_quick_links', 0, 1, 2);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (12, NULL, 'Online Classes', '#online', 'footer_quick_links', 0, 1, 3);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (13, NULL, 'Store', '#store', 'footer_quick_links', 0, 1, 4);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (14, NULL, 'Reviews', '#testimonials', 'footer_quick_links', 0, 1, 5);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (15, NULL, 'Grade 6 ICT', 'class_details.php?grade=6', 'footer_classes', 0, 1, 1);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (16, NULL, 'Grade 7 ICT', 'class_details.php?grade=7', 'footer_classes', 0, 1, 2);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (17, NULL, 'Grade 8 ICT', 'class_details.php?grade=8', 'footer_classes', 0, 1, 3);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (18, NULL, 'Grade 9 ICT', 'class_details.php?grade=9', 'footer_classes', 0, 1, 4);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (19, NULL, 'Grade 10 ICT', 'class_details.php?grade=10', 'footer_classes', 0, 1, 5);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (20, NULL, 'Grade 11 ICT', 'class_details.php?grade=11', 'footer_classes', 0, 1, 6);
INSERT INTO menu_links (id, parent_id, name, url, position, is_custom, is_visible, order_index) VALUES (21, NULL, 'Papers', 'papers.php', 'navbar', 1, 1, 10);
