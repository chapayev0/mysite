-- Database update script for new features

-- Table: paper_archive
CREATE TABLE IF NOT EXISTS `paper_archive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `grade` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `paper_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `filepath` varchar(255) NOT NULL,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `uploaded_by` (`uploaded_by`),
  CONSTRAINT `paper_archive_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: playground_categories
CREATE TABLE IF NOT EXISTS `playground_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `is_grade_based` tinyint(1) DEFAULT 0,
  `grade_level` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: playground_game_categories
CREATE TABLE IF NOT EXISTS `playground_game_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_game_category` (`game_id`,`category_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `playground_game_categories_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `playground_games` (`id`) ON DELETE CASCADE,
  CONSTRAINT `playground_game_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `playground_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: playground_games
CREATE TABLE IF NOT EXISTS `playground_games` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `image_link` varchar(500) DEFAULT NULL,
  `game_file_path` varchar(255) DEFAULT NULL,
  `game_type` enum('html','php','link') DEFAULT 'html',
  `difficulty_level` enum('easy','medium','hard') DEFAULT 'easy',
  `recommended_age` int(11) DEFAULT NULL,
  `play_count` int(11) DEFAULT 0,
  `rating` decimal(3,2) DEFAULT 0.00,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `playground_games_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: wall_posts
CREATE TABLE IF NOT EXISTS `wall_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `image_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `wall_posts_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table: messages
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

