-- Consolidated and Aligned Restore Script
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- 
-- Table structure for table `cache`
-- 
DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Table structure for table `cache_locks`
-- 
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Table structure for table `courses`
-- 
DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `course_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` int DEFAULT NULL,
  `semester` int DEFAULT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `ects` int NOT NULL,
  `hours` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courses_course_code_unique` (`course_code`),
  KEY `courses_department_id_foreign` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Dumping data for table `courses`
-- 
INSERT IGNORE INTO `courses` (`id`, `course_code`, `course_name`, `year`, `semester`, `department_id`, `ects`, `hours`, `created_at`, `updated_at`) VALUES
(1, 'SNIE1012', 'Inclusiveness', 2, 1, 1, 4, '2/0/2', NOW(), NOW()),
(2, 'CoSc2021', 'Computer Org & Architecture', 2, 1, 1, 5, '3/0/2', NOW(), NOW()),
(3, 'CoSc2031', 'Fundamentals of Programming (C++)', 2, 1, 1, 5, '3/0/3', NOW(), NOW()),
(4, 'InSy2041', 'Fundamentals of DB Systems', 2, 1, 1, 5, '3/0/3', NOW(), NOW()),
(5, 'ECEg1033', 'Fundamentals of Electricity', 2, 1, 1, 5, '3/3/0', NOW(), NOW()),
(6, 'Stat2071', 'Probability and Statistics', 2, 1, 1, 5, '3/2/0', NOW(), NOW()),
(7, 'Hist1012', 'History of Ethiopia', 2, 1, 1, 5, '3/0/0', NOW(), NOW()),
(8, 'InSy2021', 'Intro to Info Systems', 2, 1, 3, 5, '3/0/3', NOW(), NOW()),
(9, 'Econ1011', 'Intro to Economics', 2, 1, 3, 5, '3/1/0', NOW(), NOW()),
(10, 'Stat2024', 'Intro to Statistics', 2, 1, 3, 4, '2/1/0', NOW(), NOW()),
(11, 'AcFn2055', 'Fundamentals of Accounting', 2, 1, 3, 4, '2/1/0', NOW(), NOW()),
(12, 'Math2031', 'Discrete Mathematics', 2, 1, 3, 5, '3/1/0', NOW(), NOW()),
(13, 'MATH2011', 'Abstract Algebra', 2, 1, 2, 5, '3/0/0', NOW(), NOW()),
(14, 'InSy2031', 'Object Oriented SAD', 3, 1, 1, 5, '3/0/3', NOW(), NOW()),
(15, 'InTe3071', 'Multimedia Systems', 3, 1, 1, 5, '3/0/3', NOW(), NOW()),
(16, 'CoSc2051', 'OOP in Java', 3, 1, 1, 6, '4/0/3', NOW(), NOW()),
(17, 'InTe3112', 'Advanced Internet Programming', 3, 1, 1, 5, '3/0/3', NOW(), NOW()),
(18, 'InTe2021', 'Data Comm & Networks', 3, 1, 1, 5, '3/0/3', NOW(), NOW()),
(19, 'InTe3031', 'Computer Maintenance', 3, 1, 1, 6, '4/0/3', NOW(), NOW()),
(20, 'InSy3032', 'Research Methods in IS', 3, 1, 3, 5, '3/0/0', NOW(), NOW()),
(21, 'InSy3051', 'Data Mining & ML', 3, 1, 3, 5, '2/0/3', NOW(), NOW()),
(22, 'InSy3042', 'Advanced DB Systems', 3, 1, 3, 6, '3/0/3', NOW(), NOW()),
(23, 'InTe2111', 'Fundamentals of Internet Prog', 3, 1, 3, 5, '2/1/3', NOW(), NOW()),
(24, 'CoSc3022', 'Operating Systems', 3, 1, 2, 5, '2/0/3', NOW(), NOW()),
(25, 'CoSc3042', 'Algorithms Analysis', 3, 1, 2, 5, '3/0/0', NOW(), NOW()),
(26, 'CoSc3052', 'Advanced Programming', 3, 1, 2, 5, '2/0/3', NOW(), NOW()),
(27, 'InSy3033', 'Software Engineering', 3, 1, 2, 5, '3/0/0', NOW(), NOW()),
(28, 'CoSc3023', 'Microprocessor & Assembly', 3, 1, 2, 5, '2/0/3', NOW(), NOW()),
(29, 'MATH2082', 'Numerical Analysis', 3, 1, 2, 5, '3/0/0', NOW(), NOW()),
(30, 'InTe4031', 'Current Trends in IT', 4, 1, 1, 1, '1/1/0', NOW(), NOW()),
(31, 'CoSc3081', 'Artificial Intelligence', 4, 1, 1, 5, '3/2/0', NOW(), NOW()),
(32, 'InTe4042', 'Network Configuration', 4, 1, 1, 5, '3/2/3', NOW(), NOW()),
(33, 'InTe4052', 'Basic Research in IT', 4, 1, 1, 3, '2/2/0', NOW(), NOW()),
(34, 'InTe4072', 'Information Security', 4, 1, 1, 5, '3/2/3', NOW(), NOW()),
(35, 'InTe4092', 'HCI', 4, 1, 1, 5, '3/3/0', NOW(), NOW());

-- 
-- Table structure for table `departments`
-- ALIGNED: Added faculty_id
-- 
DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `faculty_id` bigint UNSIGNED DEFAULT NULL,
  `instructor_count` int NOT NULL DEFAULT '0',
  `course_count` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `class_duration` int NOT NULL DEFAULT '50' COMMENT 'In minutes',
  `lab_duration` int NOT NULL DEFAULT '100' COMMENT 'In minutes',
  `total_periods` int NOT NULL DEFAULT '8' COMMENT 'Periods per day',
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_code_unique` (`code`),
  KEY `departments_user_id_foreign` (`user_id`),
  KEY `departments_faculty_id_foreign` (`faculty_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Dumping data for table `departments`
-- 
INSERT IGNORE INTO `departments` (`id`, `code`, `name`, `user_id`, `instructor_count`, `course_count`, `created_at`, `updated_at`, `class_duration`, `lab_duration`, `total_periods`) VALUES
(1, 'IT', 'Information Technology', 2, 12, 7, '2026-03-13 02:06:54', '2026-04-04 22:27:28', 60, 100, 9),
(2, 'CS', 'computer science', 3, 0, 1, '2026-03-13 02:07:14', '2026-03-18 18:23:27', 50, 100, 8),
(3, 'IS', 'information system', 4, 0, 2, '2026-03-13 02:11:18', '2026-03-17 19:03:46', 50, 100, 8);

-- 
-- Table structure for table `department_instructor`
-- 
DROP TABLE IF EXISTS `department_instructor`;
CREATE TABLE IF NOT EXISTS `department_instructor` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `department_instructor_department_id_foreign` (`department_id`),
  KEY `department_instructor_user_id_foreign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Dumping data for table `department_instructor`
-- 
INSERT IGNORE INTO `department_instructor` (`id`, `department_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 5, '2026-03-13 02:14:17', '2026-03-13 02:14:17'),
(2, 1, 6, '2026-03-13 12:45:23', '2026-03-13 12:45:23'),
(3, 3, 6, '2026-03-13 12:46:11', '2026-03-13 12:46:11'),
(4, 1, 7, '2026-03-13 12:56:19', '2026-03-13 12:56:19'),
(5, 1, 8, '2026-03-13 12:57:00', '2026-03-13 12:57:00'),
(6, 1, 9, '2026-03-13 12:58:05', '2026-03-13 12:58:05'),
(7, 2, 7, '2026-03-15 04:29:12', '2026-03-15 04:29:12'),
(8, 1, 10, '2026-03-15 21:50:59', '2026-03-15 21:50:59'),
(11, 1, 14, '2026-04-01 21:36:55', '2026-04-01 21:36:55'),
(10, 1, 12, '2026-03-15 21:52:36', '2026-03-15 21:52:36'),
(12, 1, 15, '2026-04-01 21:37:51', '2026-04-01 21:37:51'),
(13, 1, 16, '2026-04-01 21:38:38', '2026-04-01 21:38:38'),
(14, 1, 17, '2026-04-04 13:25:50', '2026-04-04 13:25:50'),
(15, 3, 7, '2026-04-04 13:30:58', '2026-04-04 13:30:58');

-- 
-- Table structure for table `exam_exclusions`
-- 
DROP TABLE IF EXISTS `exam_exclusions`;
CREATE TABLE IF NOT EXISTS `exam_exclusions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint UNSIGNED NOT NULL,
  `course_id_1` bigint UNSIGNED NOT NULL,
  `course_id_2` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_exclusions_department_id_foreign` (`department_id`),
  KEY `exam_exclusions_course_id_1_foreign` (`course_id_1`),
  KEY `exam_exclusions_course_id_2_foreign` (`course_id_2`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Dumping data for table `exam_exclusions`
-- 
INSERT IGNORE INTO `exam_exclusions` (`id`, `department_id`, `course_id_1`, `course_id_2`, `created_at`, `updated_at`) VALUES
(10, 1, 2, 6, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(9, 1, 2, 8, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(8, 1, 2, 7, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(7, 1, 2, 3, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(6, 1, 2, 4, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(11, 1, 4, 3, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(12, 1, 4, 7, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(13, 1, 4, 8, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(14, 1, 4, 6, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(15, 1, 3, 7, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(16, 1, 3, 8, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(17, 1, 3, 6, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(18, 1, 7, 8, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(19, 1, 7, 6, '2026-03-18 13:47:06', '2026-03-18 13:47:06'),
(20, 1, 8, 6, '2026-03-18 13:47:06', '2026-03-18 13:47:06');

-- 
-- Table structure for table `exam_exclusion_groups`
-- 
DROP TABLE IF EXISTS `exam_exclusion_groups`;
CREATE TABLE IF NOT EXISTS `exam_exclusion_groups` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint UNSIGNED NOT NULL,
  `set_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_exclusion_groups_department_id_foreign` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Dumping data for table `exam_exclusion_groups`
-- 
INSERT IGNORE INTO `exam_exclusion_groups` (`id`, `department_id`, `set_name`, `created_at`, `updated_at`) VALUES
(11, 1, 'Exclusion Set #1', '2026-03-27 11:46:40', '2026-03-27 11:46:40'),
(5, 3, 'Exclusion Set #1', '2026-03-18 18:05:56', '2026-03-18 18:05:56');

-- 
-- Table structure for table `exam_exclusion_group_courses`
-- 
DROP TABLE IF EXISTS `exam_exclusion_group_courses`;
CREATE TABLE IF NOT EXISTS `exam_exclusion_group_courses` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `group_id` bigint UNSIGNED NOT NULL,
  `course_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_exclusion_group_courses_group_id_foreign` (`group_id`),
  KEY `exam_exclusion_group_courses_course_id_foreign` (`course_id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Dumping data for table `exam_exclusion_group_courses`
-- 
INSERT IGNORE INTO `exam_exclusion_group_courses` (`id`, `group_id`, `course_id`) VALUES
(1, 1, 3), (2, 1, 2), (3, 1, 4), (4, 2, 3), (5, 2, 2), (6, 2, 4), (7, 3, 8), (8, 3, 7), (9, 4, 3), (10, 4, 2), (11, 4, 4), (12, 5, 2), (13, 5, 4), (14, 6, 2), (15, 6, 9), (16, 6, 8), (17, 7, 6), (18, 7, 4), (19, 8, 3), (20, 8, 9), (21, 9, 3), (22, 9, 4), (23, 9, 9), (24, 10, 3), (25, 10, 9), (26, 11, 3), (27, 11, 9), (28, 11, 7);

-- 
-- Table structure for table `exam_instructor_availabilities`
-- 
DROP TABLE IF EXISTS `exam_instructor_availabilities`;
CREATE TABLE IF NOT EXISTS `exam_instructor_availabilities` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `instructor_id` bigint UNSIGNED NOT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `day_number` int NOT NULL,
  `period` enum('morning','afternoon') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_available` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exam_avail_dept_unique` (`instructor_id`,`day_number`,`period`,`department_id`),
  KEY `exam_instructor_availabilities_department_id_foreign` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=155 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Dumping data for table `exam_instructor_availabilities`
-- 
INSERT IGNORE INTO `exam_instructor_availabilities` (`id`, `instructor_id`, `department_id`, `day_number`, `period`, `is_available`, `created_at`, `updated_at`) VALUES
(1, 5, 1, 1, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(2, 5, 1, 1, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(3, 5, 1, 2, 'morning', 0, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(4, 5, 1, 2, 'afternoon', 0, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(5, 5, 1, 3, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(6, 5, 1, 3, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(7, 5, 1, 4, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(8, 5, 1, 4, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(9, 5, 1, 5, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(10, 5, 1, 5, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(11, 5, 1, 6, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(12, 5, 1, 6, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(13, 5, 1, 7, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(14, 5, 1, 7, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(15, 5, 1, 8, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(16, 5, 1, 8, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(17, 5, 1, 9, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(18, 5, 1, 9, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(19, 5, 1, 10, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(20, 5, 1, 10, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(21, 6, 1, 1, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(22, 6, 1, 1, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(23, 6, 1, 2, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:23:08'),
(24, 6, 1, 2, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:23:08'),
(25, 6, 1, 3, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(26, 6, 1, 3, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(27, 6, 1, 4, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:23:08'),
(28, 6, 1, 4, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:23:08'),
(29, 6, 1, 5, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(30, 6, 1, 5, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(31, 6, 1, 6, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(32, 6, 1, 6, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(33, 6, 1, 7, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(34, 6, 1, 7, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(35, 6, 1, 8, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(36, 6, 1, 8, 'afternoon', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(37, 6, 1, 9, 'morning', 1, '2026-03-18 19:19:55', '2026-03-19 22:06:47'),
(38, 6, 1, 9, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(39, 6, 1, 10, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:23:08'),
(40, 6, 1, 10, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:23:08'),
(41, 7, 1, 1, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(42, 7, 1, 1, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(43, 7, 1, 2, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(44, 7, 1, 2, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(45, 7, 1, 3, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(46, 7, 1, 3, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(47, 7, 1, 4, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(48, 7, 1, 4, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(49, 7, 1, 5, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(50, 7, 1, 5, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(51, 7, 1, 6, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(52, 7, 1, 6, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(53, 7, 1, 7, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(54, 7, 1, 7, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(55, 7, 1, 8, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(56, 7, 1, 8, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(57, 7, 1, 9, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(58, 7, 1, 9, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(59, 7, 1, 10, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(60, 7, 1, 10, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(61, 8, 1, 1, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(62, 8, 1, 1, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(63, 8, 1, 2, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(64, 8, 1, 2, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(65, 8, 1, 3, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(66, 8, 1, 3, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(67, 8, 1, 4, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(68, 8, 1, 4, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(69, 8, 1, 5, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(70, 8, 1, 5, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(71, 8, 1, 6, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(72, 8, 1, 6, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(73, 8, 1, 7, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(74, 8, 1, 7, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(75, 8, 1, 8, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(76, 8, 1, 8, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(77, 8, 1, 9, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(78, 8, 1, 9, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(79, 8, 1, 10, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(80, 8, 1, 10, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(81, 9, 1, 1, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(82, 9, 1, 1, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(83, 9, 1, 2, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(84, 9, 1, 2, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(85, 9, 1, 3, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(86, 9, 1, 3, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(87, 9, 1, 4, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(88, 9, 1, 4, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(89, 9, 1, 5, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(90, 9, 1, 5, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(91, 9, 1, 6, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(92, 9, 1, 6, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(93, 9, 1, 7, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(94, 9, 1, 7, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(95, 9, 1, 8, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(96, 9, 1, 8, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(97, 9, 1, 9, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(98, 9, 1, 9, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(99, 9, 1, 10, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(100, 9, 1, 10, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(101, 10, 1, 1, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(102, 10, 1, 1, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(103, 10, 1, 2, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(104, 10, 1, 2, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(105, 10, 1, 3, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(106, 10, 1, 3, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(107, 10, 1, 4, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(108, 10, 1, 4, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(109, 10, 1, 5, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(110, 10, 1, 5, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(111, 10, 1, 6, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(112, 10, 1, 6, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(113, 10, 1, 7, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(114, 10, 1, 7, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(115, 10, 1, 8, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(116, 10, 1, 8, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(117, 10, 1, 9, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(118, 10, 1, 9, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(119, 10, 1, 10, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(120, 10, 1, 10, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(121, 12, 1, 1, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(122, 12, 1, 1, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(123, 12, 1, 2, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(124, 12, 1, 2, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(125, 12, 1, 3, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(126, 12, 1, 3, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(127, 12, 1, 4, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(128, 12, 1, 4, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(129, 12, 1, 5, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(130, 12, 1, 5, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(131, 12, 1, 6, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(132, 12, 1, 6, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(133, 12, 1, 7, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(134, 12, 1, 7, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(135, 12, 1, 8, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(136, 12, 1, 8, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(137, 12, 1, 9, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(138, 12, 1, 9, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(139, 12, 1, 10, 'morning', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(140, 12, 1, 10, 'afternoon', 1, '2026-03-18 19:19:56', '2026-03-19 22:06:47'),
(141, 6, 3, 1, 'morning', 0, '2026-03-19 22:21:51', '2026-03-19 22:21:56'),
(142, 6, 3, 1, 'afternoon', 0, '2026-03-19 22:21:51', '2026-03-19 22:21:51'),
(143, 6, 3, 3, 'morning', 1, '2026-03-19 22:21:51', '2026-03-19 22:21:51'),
(144, 6, 3, 3, 'afternoon', 1, '2026-03-19 22:21:51', '2026-03-19 22:21:51'),
(145, 6, 3, 5, 'morning', 1, '2026-03-19 22:21:51', '2026-03-19 22:21:51'),
(146, 6, 3, 5, 'afternoon', 1, '2026-03-19 22:21:51', '2026-03-19 22:21:51'),
(147, 6, 3, 6, 'morning', 1, '2026-03-19 22:21:51', '2026-03-19 22:21:51'),
(148, 6, 3, 6, 'afternoon', 0, '2026-03-19 22:21:51', '2026-03-19 22:21:59'),
(149, 6, 3, 7, 'morning', 1, '2026-03-19 22:21:51', '2026-03-19 22:21:51'),
(150, 6, 3, 7, 'afternoon', 1, '2026-03-19 22:21:51', '2026-03-19 22:21:51'),
(151, 6, 3, 8, 'morning', 1, '2026-03-19 22:21:51', '2026-03-19 22:21:51'),
(152, 6, 3, 8, 'afternoon', 1, '2026-03-19 22:21:51', '2026-03-19 22:21:51'),
(153, 6, 3, 9, 'morning', 1, '2026-03-19 22:21:51', '2026-03-19 22:21:51'),
(154, 6, 3, 9, 'afternoon', 1, '2026-03-19 22:21:51', '2026-03-19 22:21:51');

-- 
-- Table structure for table `exam_schedules`
-- ALIGNED: Added date and invigilator columns
-- 
DROP TABLE IF EXISTS `exam_schedules`;
CREATE TABLE IF NOT EXISTS `exam_schedules` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `course_id` bigint UNSIGNED NOT NULL,
  `year` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `semester` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `day_number` int NOT NULL,
  `period` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `room_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `exam_date` date DEFAULT NULL,
  `inv1_id` bigint UNSIGNED DEFAULT NULL,
  `inv2_id` bigint UNSIGNED DEFAULT NULL,
  `inv1_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inv2_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exam_schedules_course_id_foreign` (`course_id`),
  KEY `exam_idx_context` (`year`,`semester`,`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Table structure for table `faculties`
-- 
DROP TABLE IF EXISTS `faculties`;
CREATE TABLE IF NOT EXISTS `faculties` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `faculties_name_unique` (`name`),
  UNIQUE KEY `faculties_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Dumping data for table `faculties`
-- 
INSERT IGNORE INTO `faculties` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(1, 'Faculty of Engineering', 'FOE', '2026-03-13 01:46:09', '2026-03-13 01:46:09'),
(2, 'Faculty of Informatics', 'FOI', '2026-03-13 01:46:09', '2026-03-13 01:46:09'),
(3, 'Faculty of Business and Economics', 'FBE', '2026-03-13 01:46:09', '2026-03-13 01:46:09'),
(4, 'Faculty of Social Sciences', 'FSS', '2026-03-13 01:46:09', '2026-03-13 01:46:09'),
(5, 'Faculty of Medicine', 'FOM', '2026-03-13 01:46:09', '2026-03-13 01:46:09');

-- 
-- Table structure for table `instructor_availabilities`
-- 
DROP TABLE IF EXISTS `instructor_availabilities`;
CREATE TABLE IF NOT EXISTS `instructor_availabilities` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `instructor_profile_id` bigint UNSIGNED NOT NULL,
  `day_of_week` int NOT NULL,
  `time_slot_id` int NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `department_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `instructor_availabilities_instructor_profile_id_foreign` (`instructor_profile_id`)
) ENGINE=InnoDB AUTO_INCREMENT=537 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Dumping data for table `instructor_availabilities`
-- 
INSERT IGNORE INTO `instructor_availabilities` (`id`, `instructor_profile_id`, `day_of_week`, `time_slot_id`, `type`, `department_id`, `created_at`, `updated_at`) VALUES
(10, 6, 5, 2, 'manual', 3, '2026-03-17 18:57:02', '2026-03-17 18:57:02'),
(9, 6, 5, 1, 'manual', 3, '2026-03-17 18:57:01', '2026-03-17 18:57:01'),
(11, 6, 5, 3, 'manual', 3, '2026-03-17 18:57:03', '2026-03-17 18:57:03'),
(16, 6, 3, 3, 'manual', 3, '2026-03-19 17:10:41', '2026-03-19 17:10:41'),
(536, 16, 5, 8, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(531, 10, 5, 3, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(530, 10, 5, 2, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(525, 8, 4, 5, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(526, 8, 4, 6, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(527, 10, 4, 7, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(528, 10, 4, 8, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(529, 10, 5, 1, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(523, 8, 4, 3, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(524, 8, 4, 4, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(522, 8, 3, 3, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(521, 6, 4, 2, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(520, 6, 4, 1, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(519, 6, 3, 4, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(518, 6, 3, 2, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(517, 6, 3, 1, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(516, 7, 3, 8, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(515, 7, 3, 7, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(514, 7, 3, 6, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(513, 7, 3, 5, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(512, 7, 2, 8, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(511, 10, 2, 7, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(510, 10, 2, 6, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(505, 6, 2, 1, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(506, 6, 2, 2, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(502, 7, 1, 6, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(503, 7, 1, 7, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(93, 7, 5, 4, 'manual', 3, '2026-04-04 13:31:31', '2026-04-04 13:31:31'),
(92, 7, 5, 3, 'manual', 3, '2026-04-04 13:31:30', '2026-04-04 13:31:30'),
(91, 7, 5, 2, 'manual', 3, '2026-04-04 13:31:28', '2026-04-04 13:31:28'),
(90, 7, 5, 1, 'manual', 3, '2026-04-04 13:31:27', '2026-04-04 13:31:27'),
(89, 7, 4, 4, 'manual', 3, '2026-04-04 13:31:26', '2026-04-04 13:31:26'),
(88, 7, 4, 3, 'manual', 3, '2026-04-04 13:31:25', '2026-04-04 13:31:25'),
(87, 7, 4, 2, 'manual', 3, '2026-04-04 13:31:23', '2026-04-04 13:31:23'),
(86, 7, 4, 1, 'manual', 3, '2026-04-04 13:31:22', '2026-04-04 13:31:22'),
(85, 7, 3, 4, 'manual', 3, '2026-04-04 13:31:20', '2026-04-04 13:31:20'),
(84, 7, 3, 3, 'manual', 3, '2026-04-04 13:31:18', '2026-04-04 13:31:18'),
(83, 7, 3, 2, 'manual', 3, '2026-04-04 13:31:17', '2026-04-04 13:31:17'),
(82, 7, 3, 1, 'manual', 3, '2026-04-04 13:31:16', '2026-04-04 13:31:16'),
(81, 7, 2, 4, 'manual', 3, '2026-04-04 13:31:13', '2026-04-04 13:31:13'),
(80, 7, 2, 3, 'manual', 3, '2026-04-04 13:31:13', '2026-04-04 13:31:13'),
(79, 7, 2, 2, 'manual', 3, '2026-04-04 13:31:11', '2026-04-04 13:31:11'),
(78, 7, 2, 1, 'manual', 3, '2026-04-04 13:31:10', '2026-04-04 13:31:10'),
(77, 7, 1, 4, 'manual', 3, '2026-04-04 13:31:09', '2026-04-04 13:31:09'),
(76, 7, 1, 3, 'manual', 3, '2026-04-04 13:31:08', '2026-04-04 13:31:08'),
(75, 7, 1, 2, 'manual', 3, '2026-04-04 13:31:07', '2026-04-04 13:31:07'),
(74, 7, 1, 1, 'manual', 3, '2026-04-04 13:31:06', '2026-04-04 13:31:06'),
(532, 16, 5, 4, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(533, 16, 5, 5, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(534, 16, 5, 6, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(535, 16, 5, 7, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(509, 8, 2, 5, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(507, 6, 2, 3, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(508, 8, 2, 4, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(504, 6, 1, 8, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(501, 7, 1, 5, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(500, 10, 1, 4, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(497, 6, 1, 1, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(498, 6, 1, 2, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(499, 6, 1, 3, 'system_held', 1, '2026-04-04 22:07:21', '2026-04-04 22:07:21');

-- 
-- Table structure for table `instructor_profiles`
-- 
DROP TABLE IF EXISTS `instructor_profiles`;
CREATE TABLE IF NOT EXISTS `instructor_profiles` (
  `user_id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Dumping data for table `instructor_profiles`
-- 
INSERT IGNORE INTO `instructor_profiles` (`user_id`, `first_name`, `last_name`, `department_id`, `status`, `created_at`, `updated_at`) VALUES
(5, 'Mule', 'Mok', 1, 'active', '2026-03-13 02:14:17', '2026-03-15 03:46:17'),
(6, 'zemenu', 'H', 1, 'active', '2026-03-13 12:45:23', '2026-03-13 12:45:23'),
(7, 'Abebe', 'F', 1, 'active', '2026-03-13 12:56:19', '2026-03-13 12:56:19'),
(8, 'Daniel', 'A', 1, 'active', '2026-03-13 12:57:00', '2026-03-13 12:57:00'),
(9, 'Nafyad', 'T', 1, 'active', '2026-03-13 12:58:05', '2026-03-13 12:58:05'),
(10, 'zebe', 'kebe', 1, 'active', '2026-03-15 21:50:59', '2026-03-15 21:50:59'),
(11, 'wow', 'werke', 1, 'active', '2026-03-15 21:51:46', '2026-03-15 21:51:46'),
(12, 'eze', 'weze', 1, 'active', '2026-03-15 21:52:36', '2026-03-15 21:52:36'),
(14, 'wola', 'woka', 1, 'active', '2026-04-01 21:36:55', '2026-04-01 21:36:55'),
(15, 'Askalech', 'zebe', 1, 'active', '2026-04-01 21:37:51', '2026-04-01 21:37:51'),
(16, 'majore', 'malas', 1, 'active', '2026-04-01 21:38:38', '2026-04-01 21:38:38'),
(17, 'mia', 'ka', 1, 'active', '2026-04-04 13:25:50', '2026-04-04 13:25:50');

-- 
-- Table structure for table `official_records`
-- 
DROP TABLE IF EXISTS `official_records`;
CREATE TABLE IF NOT EXISTS `official_records` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` tinyint UNSIGNED NOT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `official_records_id_number_unique` (`id_number`),
  UNIQUE KEY `official_records_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Dumping data for table `official_records`
-- 
INSERT IGNORE INTO `official_records` (`id`, `first_name`, `last_name`, `id_number`, `email`, `role_id`, `department_id`, `created_at`, `updated_at`) VALUES
(1, 'Mr Abebe', 'mo', 'IT_head', 'it@university.edu', 3, 1, '2026-03-13 01:59:21', '2026-03-13 02:02:37'),
(2, 'Dr.Degef', 'De', 'cs_head', 'cs1@university.edu', 3, 2, '2026-03-13 01:59:22', '2026-03-13 01:59:22'),
(3, 'Mr.Moke', 'Mole', 'Is_head', 'is@university.edu', 3, 3, '2026-03-13 02:09:17', '2026-03-13 02:09:17'),
(4, 'Sura', 'Nigu', 's1234', 'sura@university.edu', 5, 1, '2026-03-19 13:54:07', '2026-03-19 13:54:07');

-- 
-- Table structure for table `password_reset_tokens`
-- 
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Table structure for table `schedules`
-- 
DROP TABLE IF EXISTS `schedules`;
CREATE TABLE IF NOT EXISTS `schedules` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint UNSIGNED NOT NULL,
  `course_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `instructor_id` bigint UNSIGNED NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `day` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `period` int NOT NULL,
  `year` int NOT NULL,
  `semester` int NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `schedules_department_id_foreign` (`department_id`),
  KEY `schedules_instructor_id_foreign` (`instructor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=481 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Dumping data for table `schedules`
-- 
INSERT IGNORE INTO `schedules` (`id`, `department_id`, `course_code`, `instructor_id`, `type`, `day`, `period`, `year`, `semester`, `status`, `created_at`, `updated_at`) VALUES
(480, 1, 'as122', 16, 'Lec', 'Friday', 8, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(479, 1, 'as122', 16, 'Lec', 'Friday', 7, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(478, 1, 'as122', 16, 'Lec', 'Friday', 6, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(477, 1, 'as122', 16, 'Lec', 'Friday', 5, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(476, 1, 'as122', 16, 'Lec', 'Friday', 4, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(475, 1, 'CS111', 10, 'Lec', 'Friday', 3, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(474, 1, 'CS111', 10, 'Lec', 'Friday', 2, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(473, 1, 'CS111', 10, 'Lec', 'Friday', 1, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(472, 1, 'CS111', 10, 'Lec', 'Thursday', 8, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(471, 1, 'CS111', 10, 'Lec', 'Thursday', 7, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(470, 1, 'InSy2041', 8, 'Lec', 'Thursday', 6, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(469, 1, 'InSy2041', 8, 'Lec', 'Thursday', 5, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(468, 1, 'InSy2041', 8, 'Lec', 'Thursday', 4, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(467, 1, 'InSy2041', 8, 'Lec', 'Thursday', 3, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(466, 1, 'InSy2041', 8, 'Lec', 'Wednesday', 3, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(465, 1, 'CoSc2021', 6, 'Lec', 'Thursday', 2, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(464, 1, 'CoSc2021', 6, 'Lec', 'Thursday', 1, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(463, 1, 'CoSc2021', 6, 'Lec', 'Wednesday', 4, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(462, 1, 'CoSc2021', 6, 'Lec', 'Wednesday', 2, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(461, 1, 'CoSc2021', 6, 'Lec', 'Wednesday', 1, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(460, 1, 'CoSc2031', 7, 'Lec', 'Wednesday', 8, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(459, 1, 'CoSc2031', 7, 'Lec', 'Wednesday', 7, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(458, 1, 'CoSc2031', 7, 'Lec', 'Wednesday', 6, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(457, 1, 'CoSc2031', 7, 'Lec', 'Wednesday', 5, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(456, 1, 'CoSc2031', 7, 'Lec', 'Tuesday', 8, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(455, 1, 'CS111', 10, 'Tut', 'Tuesday', 7, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(454, 1, 'CS111', 10, 'Tut', 'Tuesday', 6, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(453, 1, 'InSy2041', 8, 'Tut', 'Tuesday', 5, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(452, 1, 'InSy2041', 8, 'Tut', 'Tuesday', 4, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(450, 1, 'CoSc2021', 6, 'Tut', 'Tuesday', 2, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(451, 1, 'CoSc2021', 6, 'Tut', 'Tuesday', 3, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(448, 1, 'CoSc2021', 6, 'Tut', 'Monday', 8, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(449, 1, 'CoSc2021', 6, 'Tut', 'Tuesday', 1, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(447, 1, 'CoSc2031', 7, 'Tut', 'Monday', 7, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(446, 1, 'CoSc2031', 7, 'Tut', 'Monday', 6, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(443, 1, 'CoSc2031', 6, 'Lab', 'Monday', 3, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(444, 1, 'CoSc2021', 10, 'Lab', 'Monday', 4, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(445, 1, 'InSy2041', 7, 'Lab', 'Monday', 5, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(441, 1, 'CoSc2031', 6, 'Lab', 'Monday', 1, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21'),
(442, 1, 'CoSc2031', 6, 'Lab', 'Monday', 2, 1, 1, 'published', '2026-04-04 22:07:21', '2026-04-04 22:07:21');

-- 
-- Table structure for table `sessions`
-- 
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Table structure for table `time_slots`
-- 
DROP TABLE IF EXISTS `time_slots`;
CREATE TABLE IF NOT EXISTS `time_slots` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'lecture',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Table structure for table `users`
-- 
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` tinyint UNSIGNED NOT NULL,
  `status` enum('active','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `faculty_id` bigint UNSIGNED DEFAULT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_faculty_id_foreign` (`faculty_id`),
  KEY `users_department_id_foreign` (`department_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 
-- Dumping data for table `users`
-- 
INSERT IGNORE INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `password`, `role_id`, `status`, `remember_token`, `created_at`, `updated_at`, `faculty_id`, `department_id`) VALUES
(1, 'admin', 'System', 'Administrator', 'admin@test.com', '$2y$12$tBFF9TOPQA0YJxTnB4dBc.430ddOM1rlPwSKYGoq19itvrw6FLcZC', 1, 'active', NULL, '2026-03-13 01:46:08', '2026-03-13 01:46:08', NULL, NULL),
(2, 'IT_head', 'Mr Abebe', 'mo', 'it@university.edu', '$2y$12$8s9LxKGkXrTJbe5jY95jX.ylZ3sNhr6dhtTK6VmmUiCD5JMMhvrbG', 3, 'active', NULL, '2026-03-13 02:03:27', '2026-04-04 13:27:34', NULL, 1),
(3, 'cs_head', 'Dr.Degef', 'De', 'cs1@university.edu', '$2y$12$qkjfp4HLB//j4s8cfkr1AOOaAQYgJId6RRIKW1HmL2s2ZwhjEl9oi', 3, 'active', NULL, '2026-03-13 02:05:35', '2026-03-19 13:54:46', NULL, 2),
(4, 'Is_head', 'Mr.Moke', 'Mole', 'is@university.edu', '$2y$12$mcEOXO3BcCJsbbVxk.riuuPub7DY6fsW90Qlzal0fjdw3g1vCWvlO', 3, 'active', NULL, '2026-03-13 02:10:16', '2026-03-13 02:10:16', NULL, 3),
(5, 'INS101', 'Mule', 'Mok', 'mule@gmail.com', '$2y$12$2UsQjqnPwiWCBRtiVjuOnub9OptQfFn3MFnPc.lmqaAXeWKxUT8U6', 4, 'active', NULL, '2026-03-13 02:14:17', '2026-03-15 03:46:17', NULL, 1),
(6, 'INST 222', 'zemenu', 'H', 'zeme@gmail.com', '$2y$12$6IRtd5SPGU/s84udaFHtqeFUT9L7V1UijQmrwwQv0sJgyVmIIxO2K', 4, 'active', NULL, '2026-03-13 12:45:23', '2026-03-13 12:45:23', NULL, 1),
(7, 'INS1011', 'Abebe', 'F', 'abe@gmail.com', '$2y$12$YL7uhcWRkdWOTRu9cQYng.DkIbvxDcKE7d1qrvHG.FmVqiYuAeSKC', 4, 'active', NULL, '2026-03-13 12:56:19', '2026-04-04 13:20:46', NULL, 1),
(8, 'INS333', 'Daniel', 'A', 'dan@gmail.com', '$2y$12$cnEWtmMJStDVWL0TFka6x.a7ZfX2.bZwa7zhxEx/ueJBfi1k8HBl6', 2, 'active', NULL, '2026-03-13 12:57:00', '2026-03-18 19:33:22', 2, 1),
(9, 'INS55', 'Nafyad', 'T', 'na@gmail.com', '$2y$12$/cFKBskFbq3He0w8AHKSA.I00COH6vy32uCRwwsmzckt1iCO2AEuG', 4, 'active', NULL, '2026-03-13 12:58:05', '2026-03-13 12:58:05', NULL, 1),
(10, 'ins456', 'zebe', 'kebe', 'ze@gmail.com', '$2y$12$QGenVwMXdAZyj9b2CRKpBeAUwfTjGBzwnd/OaEGE4j83gVnQouBke', 4, 'active', NULL, '2026-03-15 21:50:58', '2026-03-15 21:50:58', NULL, 1),
(11, 'ins798', 'wow', 'werke', 'wow@gmail.com', '$2y$12$KZa/ka9NegdvmKxCOVq2Y.rLNRpUUdwLKxFjp.Q1pUAH.1wpxYwtK', 4, 'active', NULL, '2026-03-15 21:51:46', '2026-03-15 21:51:46', NULL, 1),
(12, 'ins564', 'eze', 'weze', 'ez@gmail.com', '$2y$12$RRKjaiuD24uuz9Y/hoqqfe7dyYIIgTerO5FQ1.r9cLTQDLTMci.cC', 2, 'active', NULL, '2026-03-15 21:52:36', '2026-03-19 13:58:46', NULL, 1),
(13, 's1234', 'Sura', 'Nigu', 'sura@university.edu', '$2y$12$iFtPV8bNhZSPUx4gy/0hw.8cowOl8HBsKl4dvqmWhWOXc9K4do3xS', 5, 'active', NULL, '2026-03-19 13:56:49', '2026-03-19 13:56:49', NULL, 1),
(14, 'INS10114', 'wola', 'woka', 'wola@gmail.com', '$2y$12$.heeAWLUMiZPjRWCO3dP0.VHglombT5v5LvIjqcUHs4rPafchFSH6', 4, 'active', NULL, '2026-04-01 21:36:54', '2026-04-01 21:36:54', NULL, 1),
(15, 'INS101145', 'Askalech', 'zebe', 'aska@gmail.com', '$2y$12$M7wYpCiOa2y4.lHplHpWn.eKJyn05x3b2gkQjz5HTuaqUUmgssItu', 4, 'active', NULL, '2026-04-01 21:37:51', '2026-04-04 13:20:38', NULL, 1),
(16, 'INS333456', 'majore', 'malas', 'maj@gmail.com', '$2y$12$AVPzNjvYzDRnEOXQfue93O/M.rmc0f/poMT9bRv931/b6C6AKnD8W', 4, 'active', NULL, '2026-04-01 21:38:38', '2026-04-01 21:38:38', NULL, 1),
(17, 'ins390', 'mia', 'ka', 'mia@gmail.com', '$2y$12$k5F1dxkXS9BKbW5Oh.Kk4uWW0SJO6F17Oeyh/hXjiIWe.bKLniM1y', 4, 'active', NULL, '2026-04-04 13:25:50', '2026-04-04 13:25:50', NULL, 1);

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
