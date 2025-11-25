-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2025 at 10:42 AM
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
-- Database: `quiz_portal`
--

-- --------------------------------------------------------

--
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
(11, 8, 12, 1, 1, '{\"7\":\"b\"}', '2025-11-25 03:37:04');

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
(49, 13, '2025-11-25 03:37:33');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` enum('a','b','c','d') NOT NULL,
  `marks` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`, `marks`) VALUES
(1, 1, 'How Many Modules Are There In TOC Subject', '1', '2', '4', '5', 'd', 1),
(6, 7, 'HI', '1', '2', '4', '5', 'a', 1),
(7, 8, 'today day', '24', '25', '26', '27', 'b', 1);

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `title`, `description`, `subject_id`, `duration_minutes`, `created_by`, `is_published`, `created_at`) VALUES
(1, 'Surprise Test', '', 1, 1, 3, 1, '2025-11-10 11:05:08'),
(7, 'SUPRISE TEST', '', 2, 30, 13, 1, '2025-11-18 16:59:04'),
(8, 'first test', '', 2, 30, 13, 1, '2025-11-25 03:34:29');

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
(12, 'gowda', 'shanthaveerag@gmail.com', '$2y$10$evti8UWCrLBR2f58EOPf4uGqUE1/UUVgELnDHz9WpuHHBDvF81DPK', 'student', 'CSE', 5, '2025-11-18 16:56:49'),
(13, 'BASAVARAJ HOLIMATH', 'SS@BTI.COM', '$2y$10$KlFKd4Oe6R1WOG5Fx/wZduQ5PQTzgu3WNMZ9B0Qyu9j/7F9AmlJdW', 'teacher', NULL, NULL, '2025-11-18 16:58:41');

--
-- Indexes for dumped tables
--

--
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
-- AUTO_INCREMENT for table `attempts`
--
ALTER TABLE `attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attempts`
--
ALTER TABLE `attempts`
  ADD CONSTRAINT `attempts_ibfk_3` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attempts_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
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
