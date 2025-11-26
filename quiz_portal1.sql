-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
<<<<<<< HEAD
-- Generation Time: Nov 26, 2025 at 02:23 PM
=======
-- Generation Time: Nov 25, 2025 at 10:42 AM
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
<<<<<<< HEAD
-- Database: `quiz_portal1`
=======
-- Database: `quiz_portal`
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e
--

-- --------------------------------------------------------

--
<<<<<<< HEAD
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `target_dept` varchar(50) NOT NULL,
  `target_sem` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `title`, `message`, `target_dept`, `target_sem`, `created_by`, `created_at`, `expires_at`) VALUES
(1, 'hi', 'hlo', 'CSE', 5, 13, '2025-11-26 11:30:19', NULL);

-- --------------------------------------------------------

--
=======
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e
-- Table structure for table `attempts`
--

CREATE TABLE `attempts` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `total_marks` int(11) NOT NULL,
  `answers_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers_json`)),
  `completed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attempts`
--

INSERT INTO `attempts` (`id`, `quiz_id`, `user_id`, `score`, `total_marks`, `answers_json`, `completed_at`) VALUES
(9, 7, 12, 1, 1, '{\"6\":\"a\"}', '2025-11-18 17:00:13'),
(10, 7, 12, 0, 1, '{\"6\":\"b\"}', '2025-11-18 17:00:22'),
<<<<<<< HEAD
(11, 8, 12, 1, 1, '{\"7\":\"b\"}', '2025-11-25 03:37:04'),
(12, 9, 12, 0, 2, '{\"20\":\"a\",\"23\":\"ss\"}', '2025-11-26 10:24:26'),
(13, 9, 12, 2, 2, '{\"23\":\"ssss\"}', '2025-11-26 10:24:33'),
(14, 9, 12, 1, 2, '{\"20\":\"b\",\"23\":\"ssss\"}', '2025-11-26 10:24:46'),
(15, 9, 12, 3, 3, '{\"23\":\"ssss\",\"24\":\"how are u\\n\"}', '2025-11-26 10:26:39'),
(16, 9, 12, 2, 3, '{\"23\":\"ssss\",\"24\":\"how are \"}', '2025-11-26 10:26:52'),
(17, 9, 12, 3, 6, '{\"23\":\"\",\"24\":\"\",\"25\":[\"b\",\"d\"],\"26\":\"fine\"}', '2025-11-26 10:37:28'),
(18, 9, 12, 4, 6, '{\"23\":\"ssss\",\"24\":\"sss\",\"25\":[\"b\",\"d\"],\"26\":\"fine\"}', '2025-11-26 10:38:01'),
(19, 7, 12, 5, 6, '[]', '2025-11-26 10:39:14'),
(20, 9, 12, 1, 6, '{\"23\":\"\",\"24\":\"\",\"26\":\"\"}', '2025-11-26 10:39:21'),
(21, 9, 12, 1, 6, '{\"23\":\"\",\"24\":\"\",\"26\":\"\"}', '2025-11-26 10:39:28'),
(22, 10, 12, 1, 1, '{\"28\":\"a\"}', '2025-11-26 11:16:17'),
(23, 10, 12, 1, 1, '{\"28\":\"a\"}', '2025-11-26 11:17:08'),
(24, 11, 12, 0, 1, '{\"29\":\"\"}', '2025-11-26 12:03:37'),
(25, 12, 12, 0, 1, '{\"30\":[\"a\"]}', '2025-11-26 12:42:46');
=======
(11, 8, 12, 1, 1, '{\"7\":\"b\"}', '2025-11-25 03:37:04');
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(2, 'AIML'),
(5, 'CIVIL'),
(1, 'CSE'),
(20, 'DS'),
(3, 'ECE'),
(4, 'MECH'),
(7, 'TEST-DEPT');

-- --------------------------------------------------------

--
<<<<<<< HEAD
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `quiz_id`, `user_id`, `rating`, `message`, `created_at`) VALUES
(1, 12, 12, 4, '', '2025-11-26 12:42:52');

-- --------------------------------------------------------

--
=======
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `login_time`) VALUES
(1, 3, '2025-11-10 10:57:14'),
(2, 4, '2025-11-10 11:01:03'),
(3, 5, '2025-11-10 11:03:08'),
(4, 3, '2025-11-10 11:04:24'),
(5, 5, '2025-11-10 11:07:12'),
(6, 5, '2025-11-10 11:26:03'),
(7, 4, '2025-11-10 11:27:08'),
(8, 5, '2025-11-10 11:32:40'),
(9, 4, '2025-11-10 11:40:39'),
(10, 4, '2025-11-10 11:41:29'),
(11, 4, '2025-11-10 11:51:43'),
(12, 5, '2025-11-10 11:57:39'),
(13, 6, '2025-11-10 12:19:11'),
(14, 7, '2025-11-10 12:24:11'),
(15, 7, '2025-11-10 12:24:55'),
(16, 7, '2025-11-10 12:27:21'),
(17, 7, '2025-11-10 12:31:24'),
(18, 5, '2025-11-10 12:40:03'),
(19, 5, '2025-11-10 12:41:08'),
(20, 9, '2025-11-10 12:41:47'),
(21, 5, '2025-11-10 12:43:25'),
(22, 5, '2025-11-10 12:44:24'),
(23, 5, '2025-11-10 12:46:59'),
(24, 8, '2025-11-10 12:52:46'),
(25, 7, '2025-11-16 15:04:56'),
(26, 7, '2025-11-18 15:33:19'),
(27, 10, '2025-11-18 15:35:21'),
(28, 10, '2025-11-18 15:36:22'),
(29, 8, '2025-11-18 15:41:40'),
(30, 6, '2025-11-18 15:43:25'),
(31, 9, '2025-11-18 15:45:56'),
(32, 8, '2025-11-18 15:46:40'),
(33, 10, '2025-11-18 15:48:46'),
(34, 7, '2025-11-18 15:50:28'),
(35, 11, '2025-11-18 15:53:40'),
(36, 7, '2025-11-18 15:54:37'),
(37, 11, '2025-11-18 15:55:00'),
(38, 9, '2025-11-18 15:57:00'),
(39, 11, '2025-11-18 15:57:30'),
(40, 7, '2025-11-18 16:55:05'),
(41, 12, '2025-11-18 16:57:11'),
(42, 7, '2025-11-18 16:57:37'),
(43, 13, '2025-11-18 16:58:49'),
(44, 12, '2025-11-18 17:00:05'),
(45, 13, '2025-11-18 17:00:55'),
(46, 7, '2025-11-25 03:30:08'),
(47, 13, '2025-11-25 03:33:30'),
(48, 12, '2025-11-25 03:36:57'),
<<<<<<< HEAD
(49, 13, '2025-11-25 03:37:33'),
(50, 13, '2025-11-26 09:57:50'),
(51, 13, '2025-11-26 10:06:15'),
(52, 13, '2025-11-26 10:07:35'),
(53, 12, '2025-11-26 10:24:08'),
(54, 13, '2025-11-26 10:25:23'),
(55, 12, '2025-11-26 10:26:24'),
(56, 13, '2025-11-26 10:35:22'),
(57, 12, '2025-11-26 10:37:16'),
(58, 13, '2025-11-26 10:58:13'),
(59, 12, '2025-11-26 11:00:10'),
(60, 13, '2025-11-26 11:07:44'),
(61, 12, '2025-11-26 11:16:12'),
(62, 13, '2025-11-26 11:30:03'),
(63, 13, '2025-11-26 11:39:20'),
(64, 13, '2025-11-26 11:43:48'),
(65, 13, '2025-11-26 11:46:55'),
(66, 12, '2025-11-26 11:57:03'),
(67, 13, '2025-11-26 11:57:24'),
(68, 12, '2025-11-26 12:00:03'),
(69, 13, '2025-11-26 12:01:35'),
(70, 12, '2025-11-26 12:02:35'),
(71, 7, '2025-11-26 12:04:15'),
(72, 13, '2025-11-26 12:07:16'),
(73, 12, '2025-11-26 12:11:57'),
(74, 13, '2025-11-26 12:27:25'),
(75, 12, '2025-11-26 12:37:06'),
(76, 13, '2025-11-26 12:37:28'),
(77, 13, '2025-11-26 12:38:15'),
(78, 12, '2025-11-26 12:38:43'),
(79, 13, '2025-11-26 12:43:03'),
(80, 12, '2025-11-26 13:01:08'),
(81, 13, '2025-11-26 13:08:02'),
(82, 13, '2025-11-26 13:16:19'),
(83, 12, '2025-11-26 13:20:40'),
(84, 14, '2025-11-26 13:21:40');
=======
(49, 13, '2025-11-25 03:37:33');
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
<<<<<<< HEAD
  `type` enum('MCQ','MSQ','DESCRIPTIVE') NOT NULL DEFAULT 'MCQ',
=======
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e
  `question_text` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
<<<<<<< HEAD
  `correct_option` text DEFAULT NULL,
=======
  `correct_option` enum('a','b','c','d') NOT NULL,
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e
  `marks` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

<<<<<<< HEAD
INSERT INTO `questions` (`id`, `quiz_id`, `type`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `marks`) VALUES
(1, 1, 'MCQ', 'How Many Modules Are There In TOC Subject', '1', '2', '4', '5', 'd', 1),
(6, 7, 'MCQ', 'HI', '1', '2', '4', '5', 'a', 1),
(7, 8, 'MCQ', 'today day', '24', '25', '26', '27', 'b', 1),
(8, 7, 'MSQ', 'wewwwwwwwwww', 'w', 'w', 'ww', 'w', NULL, 1),
(9, 7, 'MSQ', 'wewwwwwwwwww', 'w', 'w', 'ww', 'w', NULL, 1),
(10, 7, 'MSQ', 'wewwwwwwwwww', 'w', 'w', 'ww', 'w', NULL, 1),
(11, 7, 'MSQ', 'wewwwwwwwwww', 'w', 'w', 'ww', 'w', NULL, 1),
(12, 7, 'MSQ', 'wewwwwwwwwww', 'w', 'w', 'ww', 'w', NULL, 1),
(13, 8, 'MCQ', 'gf', 'd', 'ds', 'fd', 'fd', NULL, 1),
(14, 8, 'MCQ', 'gf', 'd', 'ds', 'fd', 'fd', NULL, 1),
(15, 8, 'MCQ', 'gf', 'd', 'ds', 'fd', 'fd', NULL, 1),
(16, 8, 'MCQ', 'gf', 'd', 'ds', 'fd', 'fd', NULL, 1),
(17, 8, 'MCQ', 'gf', 'd', 'ds', 'fd', 'fd', NULL, 1),
(18, 8, 'MCQ', 'gf', 'd', 'ds', 'fd', 'fd', NULL, 1),
(19, 8, 'DESCRIPTIVE', 'h', '', '', '', '', NULL, 1),
(20, 9, 'MCQ', 'dd', 'dd', 'd', 'd', 'd', NULL, 1),
(21, 8, 'MCQ', 'how ', '1', '1', '1', '1', 'a', 1),
(22, 8, 'DESCRIPTIVE', 'q', '', '', '', '', 'hello', 1),
(23, 9, 'DESCRIPTIVE', 'sss', '', '', '', '', 'ssss', 1),
(24, 9, 'DESCRIPTIVE', 'hhh', '', '', '', '', 'how are u', 1),
(25, 9, 'MSQ', 'how many numbers are there in one to 10', '1', '10', '4', '010', '[\"b\",\"d\"]', 2),
(26, 9, 'DESCRIPTIVE', 'how are u', '', '', '', '', 'fine how are u ', 1),
(27, 9, 'MCQ', 'u', 'u', 'u', 'u', 'u', 'a', 9),
(28, 10, 'MCQ', 'f', 'w', '', 'f', '', 'a', 1),
(29, 11, 'DESCRIPTIVE', 'hi', '', '', '', '', 'hlo', 1),
(30, 12, 'MSQ', 'ss', 'ss', 's', 's', 's', '[\"c\",\"d\"]', 1);
=======
INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `marks`) VALUES
(1, 1, 'How Many Modules Are There In TOC Subject', '1', '2', '4', '5', 'd', 1),
(6, 7, 'HI', '1', '2', '4', '5', 'a', 1),
(7, 8, 'today day', '24', '25', '26', '27', 'b', 1);
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `duration_minutes` int(11) NOT NULL DEFAULT 30,
  `created_by` int(11) NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1,
<<<<<<< HEAD
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` datetime DEFAULT NULL
=======
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

<<<<<<< HEAD
INSERT INTO `quizzes` (`id`, `title`, `description`, `subject_id`, `duration_minutes`, `created_by`, `is_published`, `created_at`, `end_time`) VALUES
(1, 'Surprise Test', '', 1, 1, 3, 1, '2025-11-10 11:05:08', NULL),
(7, 'SUPRISE TEST', '', 2, 30, 13, 1, '2025-11-18 16:59:04', NULL),
(8, 'first test', '', 2, 30, 13, 1, '2025-11-25 03:34:29', NULL),
(9, 'd', '', 1, 30, 13, 1, '2025-11-26 10:15:27', NULL),
(10, 'hi', '', 2, 30, 13, 1, '2025-11-26 11:15:45', '2025-11-26 16:47:00'),
(11, 'hk', '', 2, 1, 13, 1, '2025-11-26 12:02:09', '2025-11-26 17:33:00'),
(12, 'sa', '', 1, 30, 13, 1, '2025-11-26 12:37:49', '2025-11-28 12:00:00');
=======
INSERT INTO `quizzes` (`id`, `title`, `description`, `subject_id`, `duration_minutes`, `created_by`, `is_published`, `created_at`) VALUES
(1, 'Surprise Test', '', 1, 1, 3, 1, '2025-11-10 11:05:08'),
(7, 'SUPRISE TEST', '', 2, 30, 13, 1, '2025-11-18 16:59:04'),
(8, 'first test', '', 2, 30, 13, 1, '2025-11-25 03:34:29');
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(50) NOT NULL,
  `department` varchar(50) NOT NULL,
  `semester` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `code`, `department`, `semester`) VALUES
(1, 'Theory of Computation', 'BCS503', 'CSE', 5),
(2, 'COMPUTER  NETWORKS', 'BCS502', 'CSE', 5);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_ids`
--

CREATE TABLE `teacher_ids` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `department` varchar(50) DEFAULT NULL,
  `assigned_to` varchar(255) NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_ids`
--

INSERT INTO `teacher_ids` (`id`, `code`, `department`, `assigned_to`, `is_used`) VALUES
(2, 'SUPERADMIN', NULL, 'Me', 1),
(5, 'BH23', 'CSE', 'BASAVARAJ HOLIMATH', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','teacher','admin') NOT NULL DEFAULT 'student',
  `department` varchar(50) DEFAULT NULL,
  `semester` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `department`, `semester`, `created_at`) VALUES
(7, 'ADMIN', 'students@bti.com', '$2y$10$rVIa6VJ0sjbaes4UDKdegORhOTO0LrQoB.2c7D/LedrRDBDbdkYDW', 'admin', NULL, NULL, '2025-11-10 12:23:47'),
<<<<<<< HEAD
(12, 'gowda', 'shanthaveerag@gmail.com', '$2y$10$/9q3Xj2nfw2Cek1VarCAX.WveD2UXdD18oVTGfoQRDq8ZA9j1A35i', 'student', 'CSE', 5, '2025-11-18 16:56:49'),
(13, 'BASAVARAJ HOLIMATH', 'SS@BTI.COM', '$2y$10$1YbnLmDu0eIZJfhV7WZM8Oj60sjEsCVhnW//1lpYTT6CePYtIfCAu', 'teacher', NULL, NULL, '2025-11-18 16:58:41'),
(14, 'praju', 'mloa@gmail.com', '$2y$10$K2Kw27Yq0lB3r8WvTsuePO7GXWR8XRWjjFZJhaTljx3Os.KOOGn1a', 'student', 'CSE', 5, '2025-11-26 13:21:27');
=======
(12, 'gowda', 'shanthaveerag@gmail.com', '$2y$10$evti8UWCrLBR2f58EOPf4uGqUE1/UUVgELnDHz9WpuHHBDvF81DPK', 'student', 'CSE', 5, '2025-11-18 16:56:49'),
(13, 'BASAVARAJ HOLIMATH', 'SS@BTI.COM', '$2y$10$KlFKd4Oe6R1WOG5Fx/wZduQ5PQTzgu3WNMZ9B0Qyu9j/7F9AmlJdW', 'teacher', NULL, NULL, '2025-11-18 16:58:41');
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e

--
-- Indexes for dumped tables
--

--
<<<<<<< HEAD
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
=======
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e
-- Indexes for table `attempts`
--
ALTER TABLE `attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
<<<<<<< HEAD
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `user_id` (`user_id`);

--
=======
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `teacher_ids`
--
ALTER TABLE `teacher_ids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
<<<<<<< HEAD
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attempts`
--
ALTER TABLE `attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;
=======
-- AUTO_INCREMENT for table `attempts`
--
ALTER TABLE `attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
<<<<<<< HEAD
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;
=======
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
<<<<<<< HEAD
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
=======
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
<<<<<<< HEAD
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
=======
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teacher_ids`
--
ALTER TABLE `teacher_ids`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
<<<<<<< HEAD
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
=======
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e

--
-- Constraints for dumped tables
--

--
<<<<<<< HEAD
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
=======
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e
-- Constraints for table `attempts`
--
ALTER TABLE `attempts`
  ADD CONSTRAINT `attempts_ibfk_3` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attempts_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
<<<<<<< HEAD
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
=======
>>>>>>> 7de41e358bec1a75de9cabd2e990b3b1d957565e
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
