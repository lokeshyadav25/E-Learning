-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 17, 2025 at 10:07 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elearning_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

DROP TABLE IF EXISTS `admins`;
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `email`) VALUES
(1, 'admin', '$2y$10$eHb7Z.BA2HdFDPdcyXRZTusL4lIRm3A93U9Fs23y9GyGTT5OBBDwK', 'admin@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
CREATE TABLE IF NOT EXISTS `assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `course_id` int NOT NULL,
  `filename` varchar(255) NOT NULL,
  `uploaded_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `course_id` (`course_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `student_id`, `course_id`, `filename`, `uploaded_at`) VALUES
(0, 0, 0, '[value-4]', '0000-00-00 00:00:00'),
(2, 3, 6, '3_6_1747472594_ashafinal_version[1].pdf', '2025-05-17 14:33:14'),
(3, 3, 6, '3_6_1747475039_COMPILER DESIGN ASSIGNMENT .pdf', '2025-05-17 15:13:59');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `faculty_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `faculty_id`, `created_at`, `price`) VALUES
(6, 'Engineering Mathematics', 'dtherjeryjery', 3, '2025-05-17 07:38:41', 0.00),
(7, 'Engineering Mathematics-II', 'adshgfkjasgiufyaiuswyfiuahfkjshkdljfhskjdhfkjashdf', 3, '2025-05-17 08:53:51', 500.00);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE IF NOT EXISTS `enrollments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `course_id` int NOT NULL,
  `enrolled_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `course_id` (`course_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `course_id`, `enrolled_at`) VALUES
(1, 3, 6, '2025-05-17 08:10:20'),
(2, 3, 7, '2025-05-17 08:55:59');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

DROP TABLE IF EXISTS `faculty`;
CREATE TABLE IF NOT EXISTS `faculty` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `full_name`, `email`, `password`, `created_at`) VALUES
(2, 'Prof. Sunil Parihar', 'sunil.parihar@sait.ac.in', '$2y$10$fyLgR.EstCTqz8rVxPnhsuBm7Z.ZVoWYpQ4jSgCqwUnomb/p4INBu', '2025-05-17 07:37:38'),
(3, 'Prof. Neelu Sharma', 'neelu.sharma@sait.ac.in', '$2y$10$0.gXKmnqJ9ie1tNcYKvg4OUT0pBSmaxnFof9DYbNy0enmj9sY3SkC', '2025-05-17 07:38:08');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','completed','failed') DEFAULT 'completed',
  `method` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

DROP TABLE IF EXISTS `quizzes`;
CREATE TABLE IF NOT EXISTS `quizzes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `course_id` int NOT NULL,
  `title` varchar(255) NOT NULL,
  `total_questions` int NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `question` text NOT NULL,
  `option_a` text NOT NULL,
  `option_b` text NOT NULL,
  `option_c` text NOT NULL,
  `option_d` text NOT NULL,
  `correct_answer` enum('A','B','C','D') NOT NULL,
  `correct_option` char(1) NOT NULL,
  `created_on` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `course_id`, `title`, `total_questions`, `created_at`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_answer`, `correct_option`, `created_on`) VALUES
(10, 6, 'Enggineering Mathematics-I', 0, '2025-05-17 15:25:16', '', '', '', '', '', 'A', '', '2025-05-17 15:25:16'),
(9, 7, 'Engineering Mathematics', 1, '2025-05-17 15:01:24', '', '', '', '', '', 'A', '', '2025-05-17 15:01:24'),
(11, 7, 'Enggineering Mathematics-II', 0, '2025-05-17 15:33:07', '', '', '', '', '', 'A', '', '2025-05-17 15:33:07'),
(12, 7, 'Enggineering Mathematics-II', 0, '2025-05-17 15:33:27', 'adfgadfhehethg', '2πr', 'πr2', '2r', 'None of the above', 'A', 'o', '2025-05-17 15:33:27');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

DROP TABLE IF EXISTS `quiz_questions`;
CREATE TABLE IF NOT EXISTS `quiz_questions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `quiz_id` int NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) DEFAULT NULL,
  `option_b` varchar(255) DEFAULT NULL,
  `option_c` varchar(255) DEFAULT NULL,
  `option_d` varchar(255) DEFAULT NULL,
  `correct_option` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_id` (`quiz_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(1, 1, 'seryztuhysijr4yhu', '2πr', 'πr2', '2r', 'None of the above', 'B'),
(2, 2, 'What is the Diasmeter of the circle ?', '2πr', 'πr2', '2r', 'None of the above', 'A'),
(3, 3, 'What is the Diameter of the circle ?', '2πr', 'πr2', '2r', 'None of the above', 'A'),
(4, 5, 'What is the Diameter of the circle ?', '2πr', 'πr2', '2r', 'None of the above', 'A'),
(5, 6, 'What is the Diameter of Circle ?', '2πr', 'πr2', '2r', 'None of the above', 'A'),
(6, 8, 'What is the Diameter of the Circle ?', '2πr', 'πr2', '2r', 'None of the above', 'A'),
(7, 9, 'What is the Diameter of Circle ?', '2πr', 'πr2', '2r', 'None of the above', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

DROP TABLE IF EXISTS `results`;
CREATE TABLE IF NOT EXISTS `results` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int DEFAULT NULL,
  `course_id` int DEFAULT NULL,
  `score` int DEFAULT NULL,
  `total_questions` int DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb3;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `student_id`, `course_id`, `score`, `total_questions`, `submitted_at`) VALUES
(1, 3, 6, 0, 1, '2025-05-17 08:37:58'),
(2, 3, 6, 0, 1, '2025-05-17 08:38:36'),
(3, 3, 6, 0, 1, '2025-05-17 08:47:48'),
(4, 3, 6, 0, 1, '2025-05-17 08:49:04'),
(5, 3, 6, 0, 1, '2025-05-17 08:49:08'),
(6, 3, 6, 0, 1, '2025-05-17 08:49:12'),
(7, 3, 6, 0, 1, '2025-05-17 08:49:16'),
(8, 3, 0, 0, 1, '2025-05-17 09:42:13');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `full_name`, `email`, `password`, `created_at`) VALUES
(3, 'Aakash Pagare', 'aakash.pagare2022@sait.ac.in', '$2y$10$6DE1a86.udaM3pYkPSVRJeG1yMNsCO6Mx2hbiFtnFRg.o88lnOVUC', '2025-05-17 08:04:08');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
