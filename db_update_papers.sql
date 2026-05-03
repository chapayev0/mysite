CREATE TABLE `paper_archive` (
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

