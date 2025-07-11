-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3309
-- Generation Time: Jul 11, 2025 at 07:07 AM
-- Server version: 10.6.18-MariaDB
-- PHP Version: 8.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `leen`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'مدير النظام', 'admin@leen.com', NULL, '$2y$12$JsVlH4ve6wL7cM.IYBjpwuRSkXz3iQxVDyZ6uW2qxW9wT8H5Ae7Ke', 'E5euzTNOciM8ucDKoO1ELyu44q3WdpN4RdmbF51s7BTO4GAbnAMojBPCK2pn', '2025-06-22 12:52:50', '2025-06-22 12:52:50'),
(2, 'مسؤول المبيعات', 'sales@leen.com', NULL, '$2y$12$TmrHCUHOtBSyw5oplBWrZegYg2TuYRHhHkSZlQfdpi0ZKS1/bIUjC', NULL, '2025-06-22 12:52:50', '2025-06-22 12:52:50'),
(3, 'خدمة العملاء', 'support@leen.com', NULL, '$2y$12$dF8Oj/LQbNFrvXeUUaIM6O.eUIAK8yzpn1OCgKibXE1bY9lWW8lFO', NULL, '2025-06-22 12:52:51', '2025-06-22 12:52:51'),
(4, 'مسؤول المحتوى', 'content@leen.com', NULL, '$2y$12$VXp3X.lCoagULWXGTLh.xuhlVn6/571AQuQqo9NSBDfAsxaz38P96', NULL, '2025-06-22 12:52:51', '2025-06-22 12:52:51');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `is_active`, `display_order`, `image`, `created_at`, `updated_at`) VALUES
(1, 'الشعر', 'alshaar', 'وصف تصنيف قص الشعر', 1, 0, 'images/categories/1751237199.jpg', '2025-06-22 15:47:31', '2025-06-29 19:46:39'),
(2, 'البشرة', 'albshr', 'وصف تصنيف تنظيف البشرة', 1, 0, 'images/categories/1751237211.jpg', '2025-06-22 15:47:54', '2025-06-29 19:46:51'),
(3, 'صبغة', 'sbgh', 'وصف تصنيف صبغ الشعر', 1, 0, 'images/categories/1751237218.jpg', '2025-06-29 12:54:31', '2025-06-29 19:46:58'),
(4, 'أظافر', 'athafr', 'وصف تفصيلي لتصنيف الأظافر', 1, 0, 'images/categories/1751237226.jpg', '2025-06-29 12:55:12', '2025-06-29 19:47:06'),
(5, 'ميك اب', 'myk-ab', 'وصف تفصيلي لتصنيف الميك اب', 1, 0, 'images/categories/1751237234.jpg', '2025-06-29 12:55:58', '2025-06-29 19:47:14'),
(6, 'مساج', 'msag', 'وصف تفصيلي لتصنيف المساج', 1, 0, 'images/categories/1751237240.jpg', '2025-06-29 12:57:39', '2025-06-29 19:47:20');

-- --------------------------------------------------------

--
-- Table structure for table `chat_rooms`
--

CREATE TABLE `chat_rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `image` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_latitude` decimal(10,7) DEFAULT NULL,
  `last_longitude` decimal(10,7) DEFAULT NULL,
  `last_location_update` timestamp NULL DEFAULT NULL,
  `location_tracking_enabled` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `first_name`, `last_name`, `email`, `password`, `phone`, `phone_verified_at`, `status`, `image`, `location`, `remember_token`, `created_at`, `updated_at`, `last_latitude`, `last_longitude`, `last_location_update`, `location_tracking_enabled`) VALUES
(1, 'Sayed', 'Khattab', 'customer@leen.com', '$2y$12$jPSB1yj0GUhBlX9zAXS91OoRmifhKfvzFtDaJV0b3rVQnePtAafN6', '01206994677', NULL, 'active', 'images/customers/1750618158.jpg', 'الرياض - حي القدس', NULL, '2025-06-22 15:49:18', '2025-06-22 15:49:18', NULL, NULL, NULL, 1),
(2, 'سيد', 'خطاب', 'sayed@moo.com', '$2y$12$FTcj3FJSb.0kd/Mwjyjq/uZmf2k5///Tz7tGFuGMK5rvnZsnm9Jpu', '96654830787870', NULL, 'active', NULL, 'الرياض', NULL, '2025-06-26 20:34:52', '2025-06-26 20:37:56', NULL, NULL, NULL, 1),
(4, 'محمد', 'خطاب', 'm@s.com', '$2y$12$Zxcyj1C/W32ZKuywqVrGXeI2lo7K5jkw6rFZIiKjhYHlZzy37CMXy', '966548303030', NULL, 'active', NULL, 'الرياض', NULL, '2025-06-27 21:28:00', '2025-06-27 21:28:00', NULL, NULL, NULL, 1),
(5, 'سيد', 'خطاب', 'ss@mm.com', '$2y$12$ya6gyQ8kQGyW5rT74OfrB.rAKLnoOr7fpo8SyltZLCmaUcdk7/3o2', '9665483898980', NULL, 'active', '/storage/customers/KEkjDraquzS06H3vpKcQqrVliWkF7kMp00DXyXm7.jpg', 'الدمام / الشرقية', NULL, '2025-06-27 21:36:08', '2025-07-03 01:38:00', NULL, NULL, NULL, 1),
(6, 'sss', 'mmm', 'sss@mmm.net', '$2y$12$4CO6GmQd6fZf10THdkgIMe7GVinXiPG9n8k6XfE/qW10cE56GCNc6', '966545503000', NULL, 'active', NULL, 'الدمام', NULL, '2025-06-27 21:46:04', '2025-06-27 21:46:04', NULL, NULL, NULL, 1),
(7, 'seso', 'khattab', 'seso@khattab.com', '$2y$12$blWPgRgqzxob3GnzDEwbge1ldNupK2lNW1GQ.WUUC/8zq.O4UgTqW', '966548303001', NULL, 'active', NULL, 'الرياض الوسطى', NULL, '2025-06-27 21:52:03', '2025-06-27 21:52:03', NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `discount_applications`
--

CREATE TABLE `discount_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `coupon_id` bigint(20) UNSIGNED DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `discount_type` varchar(255) NOT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `work_start_time` time DEFAULT NULL COMMENT 'وقت بدء الدوام اليومي',
  `work_end_time` time DEFAULT NULL COMMENT 'وقت انتهاء الدوام اليومي',
  `working_days` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'أيام العمل في الأسبوع' CHECK (json_valid(`working_days`)),
  `position` varchar(255) DEFAULT NULL COMMENT 'المسمى الوظيفي',
  `email` varchar(255) DEFAULT NULL COMMENT 'البريد الإلكتروني للموظف',
  `photo` varchar(255) DEFAULT NULL COMMENT 'صورة الموظف',
  `experience_years` int(11) DEFAULT 0 COMMENT 'سنوات الخبرة',
  `specialization` varchar(255) DEFAULT NULL COMMENT 'التخصص',
  `max_bookings_per_day` int(11) DEFAULT 10 COMMENT 'الحد الأقصى للحجوزات اليومية',
  `is_available` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'متاح للحجز',
  `completed_bookings_count` int(11) NOT NULL DEFAULT 0 COMMENT 'عدد الحجوزات المكتملة',
  `rating` decimal(3,2) DEFAULT NULL COMMENT 'تقييم الموظف (متوسط التقييمات)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `seller_id`, `name`, `phone`, `status`, `created_at`, `updated_at`, `work_start_time`, `work_end_time`, `working_days`, `position`, `email`, `photo`, `experience_years`, `specialization`, `max_bookings_per_day`, `is_available`, `completed_bookings_count`, `rating`) VALUES
(1, 1, 'موظف 1 - بيوتي سنتر', '966524779483', 'active', '2025-07-01 10:14:30', '2025-07-01 10:14:30', '10:00:00', '16:00:00', '[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\",\"Saturday\"]', 'مصور', 'employee1.بيوتيسنتر@example.com', NULL, 2, 'مصور فوتوغرافي', 11, 0, 32, 4.50),
(2, 1, 'موظف 2 - بيوتي سنتر', '966521546949', 'active', '2025-07-01 10:14:30', '2025-07-01 10:14:30', '08:00:00', '19:00:00', '[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\",\"Saturday\"]', 'فني متخصص', 'employee2.بيوتيسنتر@example.com', NULL, 8, 'خبير مانيكير وباديكير', 6, 0, 28, 4.70),
(3, 2, 'موظف 1 - مساج سنتر', '966560683950', 'active', '2025-07-01 10:14:30', '2025-07-01 10:14:30', '09:00:00', '17:00:00', '[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\",\"Saturday\"]', 'فني متخصص', 'employee1.مساجسنتر@example.com', NULL, 9, 'خبير حناء', 13, 1, 18, 3.50),
(4, 2, 'موظف 2 - مساج سنتر', '966598961331', 'active', '2025-07-01 10:14:30', '2025-07-01 10:14:30', '08:00:00', '20:00:00', '[\"Sunday\",\"Monday\",\"Thursday\",\"Friday\",\"Saturday\"]', 'فني متخصص', 'employee2.مساجسنتر@example.com', NULL, 8, 'خبير تجميل', 13, 0, 47, 3.90),
(5, 2, 'موظف 3 - مساج سنتر', '966588826496', 'active', '2025-07-01 10:14:30', '2025-07-01 10:14:30', '10:00:00', '19:00:00', '[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\",\"Saturday\"]', 'مصور', 'employee3.مساجسنتر@example.com', NULL, 7, 'مصور فوتوغرافي', 7, 1, 35, 3.30),
(6, 3, 'موظف 1 - صالون لافندر', '966507466506', 'active', '2025-07-01 10:14:30', '2025-07-01 10:14:30', '10:00:00', '16:00:00', '[\"Sunday\",\"Tuesday\",\"Thursday\",\"Friday\",\"Saturday\"]', 'مصمم', 'employee1.صالونلافندر@example.com', NULL, 7, 'مصمم أزياء', 11, 1, 33, 4.80),
(7, 3, 'موظف 2 - صالون لافندر', '966520645367', 'active', '2025-07-01 10:14:30', '2025-07-01 10:14:30', '08:00:00', '18:00:00', '[\"Sunday\",\"Wednesday\",\"Saturday\"]', 'مصفف شعر', 'employee2.صالونلافندر@example.com', NULL, 3, 'مصفف شعر للاستوديو', 8, 0, 41, 4.50),
(8, 3, 'موظف 3 - صالون لافندر', '966519356150', 'active', '2025-07-01 10:14:30', '2025-07-01 10:14:30', '08:00:00', '19:00:00', '[\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Saturday\"]', 'مصفف شعر', 'employee3.صالونلافندر@example.com', NULL, 5, 'مصفف شعر للاستوديو', 10, 0, 4, 3.30),
(9, 3, 'موظف 4 - صالون لافندر', '966541905484', 'active', '2025-07-01 10:14:30', '2025-07-01 10:14:30', '09:00:00', '17:00:00', '[\"Monday\",\"Tuesday\",\"Thursday\"]', 'خبير تجميل', 'employee4.صالونلافندر@example.com', NULL, 5, 'خبير مكياج للاستوديو', 12, 0, 25, 3.10),
(10, 3, 'موظف 5 - صالون لافندر', '966583240137', 'active', '2025-07-01 10:14:30', '2025-07-01 10:14:30', '10:00:00', '18:00:00', '[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\"]', 'فني متخصص', 'employee5.صالونلافندر@example.com', NULL, 10, 'خبير إضاءة', 10, 0, 34, 4.30),
(11, 4, 'موظف 1 - صالون الرحاب', '966575334529', 'active', '2025-07-01 10:14:30', '2025-07-01 10:14:30', '10:00:00', '19:00:00', '[\"Tuesday\",\"Wednesday\",\"Friday\",\"Saturday\"]', 'فني متخصص', 'employee1.صالونالرحاب@example.com', NULL, 7, 'مدلك', 10, 0, 33, 3.90),
(12, 4, 'موظف 2 - صالون الرحاب', '966523615764', 'active', '2025-07-01 10:14:30', '2025-07-01 10:14:30', '08:00:00', '20:00:00', '[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]', 'مصفف شعر', 'employee2.صالونالرحاب@example.com', NULL, 2, 'مصفف شعر', 13, 0, 29, 3.00),
(13, 4, 'موظف 3 - صالون الرحاب', '966513490496', 'active', '2025-07-01 10:14:30', '2025-07-01 10:14:30', '10:00:00', '20:00:00', '[\"Tuesday\",\"Wednesday\",\"Friday\",\"Saturday\"]', 'مصفف شعر', 'employee3.صالونالرحاب@example.com', NULL, 9, 'مصفف شعر', 7, 1, 2, 4.80),
(14, 7, 'مريم محمود', '966580992917', 'active', '2025-07-01 10:14:30', '2025-07-01 11:07:54', '09:00:00', '19:00:00', '[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]', 'فني متخصص', 'employee1.مركزتجميلالرياض@example.com', '1751378874_6863ebbad121d.jpg', 3, 'خبير مانيكير وباديكير', 8, 0, 13, 3.40),
(16, 7, 'وسام عبدالله', '548303030', 'active', '2025-07-01 10:48:35', '2025-07-01 10:48:35', '09:00:00', '18:00:00', '[\"Sunday\",\"Monday\",\"Tuesday\",\"Wednesday\",\"Thursday\",\"Friday\"]', 'مصفف شعر', 'we@we.com', '1751377715_6863e733b6d4e.jpg', 5, 'الشعر', 25, 1, 0, NULL),
(17, 7, 'اسماء علي', '0555667788', 'active', '2025-07-02 10:38:46', '2025-07-03 01:36:34', '09:00:00', '20:00:00', NULL, 'مصففة شعر', 'sss@mmmmmm.com', '1751506594_6865dea21c2c8.jpg', 8, 'الشعر والبشرة', 8, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `featured_professionals`
--

CREATE TABLE `featured_professionals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `featured_title` varchar(255) DEFAULT NULL,
  `featured_description` text DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `featured_services`
--

CREATE TABLE `featured_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `service_type` varchar(255) NOT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `home_services`
--

CREATE TABLE `home_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `sub_category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `gender` enum('male','female','both') NOT NULL DEFAULT 'both',
  `service_details` text DEFAULT NULL,
  `employees` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`employees`)),
  `price` decimal(10,2) NOT NULL,
  `booking_status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `discount` tinyint(1) DEFAULT 0,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `points` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'مدة الخدمة بالدقائق',
  `description` text DEFAULT NULL COMMENT 'وصف مفصل للخدمة',
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'صور الخدمة' CHECK (json_valid(`images`)),
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'نسبة الخصم',
  `discounted_price` decimal(10,2) DEFAULT NULL COMMENT 'السعر بعد الخصم'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_services`
--

INSERT INTO `home_services` (`id`, `seller_id`, `category_id`, `sub_category_id`, `name`, `gender`, `service_details`, `employees`, `price`, `booking_status`, `discount`, `percentage`, `points`, `created_at`, `updated_at`, `duration`, `description`, `images`, `discount_percentage`, `discounted_price`) VALUES
(1, 7, 1, 2, 'خدمة فرد الشعر', 'both', NULL, NULL, 200.00, 'available', 0, 0.00, 0, '2025-06-29 15:32:23', '2025-07-01 09:54:33', 30, 'خدمة فرد الشعر بالمواد الطبيعية التي يحتاجها الشعر الجاف والمتقصف والتي قد تكون ضارة', '\"[\\\"1751374473_3UwA6B7bQB.jpg\\\",\\\"1751374473_3gVN0j35r6.jpg\\\"]\"', 0.00, 200.00),
(2, 7, 1, 4, 'صبغ شعر نسائي', 'both', NULL, NULL, 280.00, 'available', 0, 0.00, 0, '2025-06-29 15:34:19', '2025-07-01 09:52:37', 60, 'وصف للتعديل على خدمة صبغ الشعر النسائي باستخدام افضل مواد طبيعية في العالم', '\"[\\\"1751374357_ZpLxJLZngW.jpg\\\",\\\"1751374357_W8mJDp8ro8.jpg\\\",\\\"1751374357_LWu03Bbc4Q.jpg\\\"]\"', 0.00, 280.00),
(3, 7, 1, 2, 'تصفيف الشعر كيرلي', 'both', NULL, NULL, 55.00, 'available', 0, 0.00, 0, '2025-06-29 16:40:03', '2025-07-01 09:50:55', 60, 'وصف خدمة تصفيف الشعر بطريقة كيرلي بمواد طبيعية وأساليب عصرية', '\"[\\\"1751374255_ioMyH9DfSw.jpg\\\",\\\"1751374255_YpqbpunSuI.jpg\\\"]\"', 0.00, 55.00);

-- --------------------------------------------------------

--
-- Table structure for table `home_service_bookings`
--

CREATE TABLE `home_service_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `home_service_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `payment_status` enum('pending','partially_paid','paid','failed','refunded') DEFAULT 'pending',
  `booking_status` enum('pending','confirmed','completed','cancelled','rejected') NOT NULL DEFAULT 'pending',
  `location` varchar(255) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL,
  `request_rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `home_service_bookings`
--

INSERT INTO `home_service_bookings` (`id`, `home_service_id`, `customer_id`, `seller_id`, `employee_id`, `payment_id`, `date`, `start_time`, `payment_status`, `booking_status`, `location`, `paid_amount`, `request_rejection_reason`, `created_at`, `updated_at`) VALUES
(3, 3, 5, 7, 1, 9, '2025-07-04', '10:00:00', 'paid', 'pending', 'عبووور', 55.00, NULL, '2025-07-03 00:37:37', '2025-07-03 00:37:39'),
(4, 2, 5, 7, 1, 11, '2025-07-05', '08:00:00', 'pending', 'pending', 'العبور - القاهرة', 280.00, 'ملاحظة عدم التأخير', '2025-07-03 01:39:06', '2025-07-03 01:39:34');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `chat_room_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `sender_type` enum('seller','customer') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_06_21_143128_create_personal_access_tokens_table', 1),
(5, '2025_06_21_150000_create_admins_table', 1),
(6, '2025_06_21_150001_create_sellers_table', 1),
(7, '2025_06_21_150002_create_customers_table', 1),
(8, '2025_06_21_150003_create_categories_table', 1),
(9, '2025_06_21_150004_create_sub_categories_table', 1),
(10, '2025_06_21_150005_create_employees_table', 1),
(11, '2025_06_21_150006_create_home_services_table', 1),
(12, '2025_06_21_150007_create_studio_services_table', 1),
(13, '2025_06_21_150008_create_home_service_bookings_table', 1),
(14, '2025_06_21_150009_create_studio_service_bookings_table', 1),
(15, '2025_06_21_150010_create_chat_rooms_table', 1),
(16, '2025_06_21_150011_create_messages_table', 1),
(17, '2025_06_21_150012_create_notifications_table', 1),
(18, '2025_06_21_182224_create_payments_table', 1),
(19, '2025_06_21_183816_create_phone_verifications_table', 1),
(20, '2025_06_21_183914_create_payment_transactions_table', 1),
(21, '2025_06_21_183936_create_sms_notifications_table', 1),
(22, '2025_06_21_184016_add_payment_id_to_booking_tables', 1),
(23, '2025_06_21_222145_add_location_fields_to_users_tables', 2),
(24, '2023_07_12_000000_create_promotional_banners_table', 3),
(25, '2023_07_12_000001_create_featured_services_table', 3),
(26, '2023_07_12_000002_create_featured_professionals_table', 3),
(27, '2023_07_12_000003_create_special_offers_table', 3),
(28, '2023_07_12_000004_add_display_fields_to_categories_table', 3),
(29, '2023_07_12_000005_add_display_fields_to_sub_categories_table', 3),
(30, '2023_07_12_000006_add_linking_fields_to_promotional_banners_table', 4),
(31, '2025_06_26_200402_add_commercial_register_to_sellers_table', 5),
(32, '2025_06_29_192253_add_missing_fields_to_home_services_table', 6),
(33, '2025_06_29_192332_add_missing_fields_to_studio_services_table', 7),
(34, '2023_08_01_000001_update_employees_table_add_additional_fields', 8),
(35, '2023_07_20_000001_create_order_references_table', 9),
(36, '2023_08_15_000001_add_partial_payment_fields_to_payments_table', 10),
(37, '2023_08_15_000002_update_booking_tables_add_partially_paid_status', 10);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `sender_type` enum('admin','seller','customer','system') NOT NULL,
  `content` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `category` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_references`
--

CREATE TABLE `order_references` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `reference_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_references`
--

INSERT INTO `order_references` (`id`, `payment_id`, `reference_id`) VALUES
(1, 2, '391754'),
(3, 4, '391829'),
(4, 5, '391831'),
(5, 6, '391854'),
(6, 7, '391862'),
(7, 8, '391888'),
(8, 9, '391897'),
(9, 10, '391918'),
(10, 11, '391959'),
(11, 12, '391977'),
(12, 13, '391995'),
(13, 14, '392011'),
(14, 15, '392091'),
(15, 16, '392107'),
(16, 17, '392113'),
(17, 18, '392142'),
(18, 19, '392185'),
(19, 20, '392518'),
(20, 21, '392525'),
(21, 22, '392530'),
(22, 23, '392539'),
(23, 24, '392564'),
(24, 25, '392593'),
(25, 26, '392608'),
(26, 27, '392627'),
(27, 28, '392643'),
(28, 34, '394705'),
(29, 35, '394752'),
(30, 36, '394767'),
(31, 37, '394779'),
(32, 38, '394843'),
(33, 39, '394895'),
(34, 40, '394941'),
(35, 41, '394962'),
(36, 42, '394987'),
(37, 43, '395022'),
(38, 44, '395038'),
(39, 45, '395048'),
(40, 46, '395087'),
(42, 1, '395300'),
(43, 2, '395314'),
(45, 4, '395390'),
(46, 5, '395456'),
(47, 6, '396547'),
(48, 7, '396551'),
(49, 8, '396554'),
(50, 9, '396565'),
(51, 10, '396572'),
(52, 11, '396652');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_partial` tinyint(1) NOT NULL DEFAULT 0,
  `deposit_percentage` decimal(5,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `reference_id` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_data` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `amount`, `paid_amount`, `is_partial`, `deposit_percentage`, `status`, `reference_id`, `user_id`, `transaction_id`, `payment_method`, `payment_data`, `created_at`, `updated_at`) VALUES
(4, 250.00, 50.00, 1, 20.00, 'Pending', '395390', 5, NULL, 'paymob', '\"{\\\"created_at\\\":\\\"2025-07-02T17:38:05+00:00\\\",\\\"ip\\\":\\\"192.168.1.34\\\",\\\"user_agent\\\":\\\"Dart\\\\\\/3.8 (dart:io)\\\"}\"', '2025-07-02 14:38:05', '2025-07-02 14:38:06'),
(5, 250.00, 125.00, 1, 50.00, 'Pending', '395456', 5, NULL, 'paymob', '\"{\\\"created_at\\\":\\\"2025-07-02T17:50:54+00:00\\\",\\\"ip\\\":\\\"192.168.1.34\\\",\\\"user_agent\\\":\\\"Dart\\\\\\/3.8 (dart:io)\\\"}\"', '2025-07-02 14:50:54', '2025-07-02 14:50:55'),
(6, 250.00, 200.00, 1, 80.00, 'Pending', '396547', 5, NULL, 'paymob', '\"{\\\"created_at\\\":\\\"2025-07-03T00:21:45+00:00\\\",\\\"ip\\\":\\\"41.233.217.16\\\",\\\"user_agent\\\":\\\"Dart\\\\\\/3.8 (dart:io)\\\"}\"', '2025-07-03 00:21:45', '2025-07-03 00:21:45'),
(7, 250.00, 200.00, 1, 80.00, 'Pending', '396551', 5, NULL, 'paymob', '\"{\\\"created_at\\\":\\\"2025-07-03T00:24:50+00:00\\\",\\\"ip\\\":\\\"41.233.217.16\\\",\\\"user_agent\\\":\\\"Dart\\\\\\/3.8 (dart:io)\\\"}\"', '2025-07-03 00:24:50', '2025-07-03 00:24:51'),
(8, 250.00, 225.00, 1, 90.00, 'Pending', '396554', 5, NULL, 'paymob', '\"{\\\"created_at\\\":\\\"2025-07-03T00:28:45+00:00\\\",\\\"ip\\\":\\\"41.233.217.16\\\",\\\"user_agent\\\":\\\"Dart\\\\\\/3.8 (dart:io)\\\"}\"', '2025-07-03 00:28:45', '2025-07-03 00:28:46'),
(9, 55.00, 0.00, 0, NULL, 'Pending', '396565', 5, NULL, 'paymob', '\"{\\\"created_at\\\":\\\"2025-07-03T00:37:39+00:00\\\",\\\"ip\\\":\\\"41.233.217.16\\\",\\\"user_agent\\\":\\\"Dart\\\\\\/3.8 (dart:io)\\\"}\"', '2025-07-03 00:37:39', '2025-07-03 00:37:40'),
(10, 250.00, 0.00, 0, NULL, 'Pending', '396572', 5, NULL, 'paymob', '\"{\\\"created_at\\\":\\\"2025-07-03T00:40:53+00:00\\\",\\\"ip\\\":\\\"41.233.217.16\\\",\\\"user_agent\\\":\\\"Dart\\\\\\/3.8 (dart:io)\\\"}\"', '2025-07-03 00:40:53', '2025-07-03 00:40:54'),
(11, 280.00, 238.00, 1, 85.00, 'Pending', '396652', 5, NULL, 'paymob', '\"{\\\"created_at\\\":\\\"2025-07-03T01:39:34+00:00\\\",\\\"ip\\\":\\\"41.233.217.16\\\",\\\"user_agent\\\":\\\"Dart\\\\\\/3.8 (dart:io)\\\"}\"', '2025-07-03 01:39:34', '2025-07-03 01:39:35');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL,
  `transaction_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`transaction_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `payment_id`, `transaction_id`, `type`, `amount`, `status`, `transaction_data`, `created_at`, `updated_at`) VALUES
(1, 7, '252631', 'card', 200.00, 'success', '\"{\\\"id\\\":252631,\\\"pending\\\":false,\\\"amount_cents\\\":20000,\\\"success\\\":true,\\\"is_auth\\\":false,\\\"is_capture\\\":false,\\\"is_standalone_payment\\\":true,\\\"is_voided\\\":false,\\\"is_refunded\\\":false,\\\"is_3d_secure\\\":true,\\\"integration_id\\\":11784,\\\"profile_id\\\":8506,\\\"has_parent_transaction\\\":false,\\\"order\\\":{\\\"id\\\":396551,\\\"created_at\\\":\\\"2025-07-03T03:24:51.110457+03:00\\\",\\\"delivery_needed\\\":false,\\\"merchant\\\":{\\\"id\\\":8506,\\\"created_at\\\":\\\"2025-04-18T16:33:32.223343+03:00\\\",\\\"phones\\\":[\\\"+966546608060\\\"],\\\"company_emails\\\":null,\\\"company_name\\\":\\\"Leen\\\",\\\"state\\\":null,\\\"country\\\":\\\"SAU\\\",\\\"city\\\":\\\"temp\\\",\\\"postal_code\\\":null,\\\"street\\\":null},\\\"collector\\\":null,\\\"amount_cents\\\":20000,\\\"shipping_data\\\":{\\\"id\\\":224208,\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"street\\\":\\\"NA\\\",\\\"building\\\":\\\"NA\\\",\\\"floor\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"city\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"extra_description\\\":null,\\\"shipping_method\\\":\\\"UNK\\\",\\\"order_id\\\":396551,\\\"order\\\":396551},\\\"currency\\\":\\\"SAR\\\",\\\"is_payment_locked\\\":false,\\\"is_return\\\":false,\\\"is_cancel\\\":false,\\\"is_returned\\\":false,\\\"is_canceled\\\":false,\\\"merchant_order_id\\\":\\\"7_1751502290\\\",\\\"wallet_notification\\\":null,\\\"paid_amount_cents\\\":20000,\\\"notify_user_with_email\\\":false,\\\"items\\\":[{\\\"name\\\":\\\"Deposit Payment #7\\\",\\\"description\\\":\\\"Deposit payment for service booking\\\",\\\"amount_cents\\\":20000,\\\"quantity\\\":1}],\\\"order_url\\\":\\\"https:\\\\\\/\\\\\\/ksa.paymob.com\\\\\\/standalone\\\\\\/?ref=i_LRR2ZkZOZ2k0OFNocXM0VTcxeGp0Y1ZhQT09X2pmREtJYnBnZEl6V041cjBDTjdlRlE9PQ\\\",\\\"commission_fees\\\":0,\\\"delivery_fees_cents\\\":0,\\\"delivery_vat_cents\\\":0,\\\"payment_method\\\":\\\"tbc\\\",\\\"merchant_staff_tag\\\":null,\\\"api_source\\\":\\\"OTHER\\\",\\\"data\\\":[],\\\"payment_status\\\":\\\"PAID\\\"},\\\"created_at\\\":\\\"2025-07-03T03:25:27.932837+03:00\\\",\\\"transaction_processed_callback_responses\\\":[],\\\"currency\\\":\\\"SAR\\\",\\\"source_data\\\":{\\\"pan\\\":\\\"1111\\\",\\\"type\\\":\\\"card\\\",\\\"tenure\\\":null,\\\"sub_type\\\":\\\"Visa\\\"},\\\"api_source\\\":\\\"IFRAME\\\",\\\"terminal_id\\\":null,\\\"merchant_commission\\\":0,\\\"installment\\\":null,\\\"discount_details\\\":[],\\\"is_void\\\":false,\\\"is_refund\\\":false,\\\"data\\\":{\\\"gateway_integration_pk\\\":11784,\\\"klass\\\":\\\"MigsPayment\\\",\\\"created_at\\\":\\\"2025-07-03T00:25:44.501586\\\",\\\"amount\\\":20000,\\\"currency\\\":\\\"SAR\\\",\\\"migs_order\\\":{\\\"acceptPartialAmount\\\":false,\\\"amount\\\":200,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"chargeback\\\":{\\\"amount\\\":0,\\\"currency\\\":\\\"SAR\\\"},\\\"creationTime\\\":\\\"2025-07-03T00:25:40.272Z\\\",\\\"currency\\\":\\\"SAR\\\",\\\"id\\\":\\\"aa396551\\\",\\\"lastUpdatedTime\\\":\\\"2025-07-03T00:25:44.440Z\\\",\\\"merchantAmount\\\":200,\\\"merchantCategoryCode\\\":\\\"7372\\\",\\\"merchantCurrency\\\":\\\"SAR\\\",\\\"status\\\":\\\"CAPTURED\\\",\\\"totalAuthorizedAmount\\\":200,\\\"totalCapturedAmount\\\":200,\\\"totalRefundedAmount\\\":0},\\\"merchant\\\":\\\"TEST601108800\\\",\\\"migs_result\\\":\\\"SUCCESS\\\",\\\"migs_transaction\\\":{\\\"acquirer\\\":{\\\"batch\\\":20250703,\\\"date\\\":\\\"0703\\\",\\\"id\\\":\\\"NCB_S2I\\\",\\\"merchantId\\\":\\\"601108800\\\",\\\"settlementDate\\\":\\\"2025-07-03\\\",\\\"timeZone\\\":\\\"+0300\\\",\\\"transactionId\\\":\\\"123456789012345\\\"},\\\"amount\\\":200,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"authorizationCode\\\":\\\"225634\\\",\\\"currency\\\":\\\"SAR\\\",\\\"id\\\":\\\"252631\\\",\\\"receipt\\\":\\\"518400225634\\\",\\\"source\\\":\\\"INTERNET\\\",\\\"stan\\\":\\\"225634\\\",\\\"terminal\\\":\\\"NCBS2I02\\\",\\\"type\\\":\\\"PAYMENT\\\"},\\\"txn_response_code\\\":\\\"APPROVED\\\",\\\"acq_response_code\\\":\\\"00\\\",\\\"message\\\":\\\"Approved\\\",\\\"merchant_txn_ref\\\":\\\"252631\\\",\\\"order_info\\\":\\\"aa396551\\\",\\\"receipt_no\\\":\\\"518400225634\\\",\\\"transaction_no\\\":\\\"123456789012345\\\",\\\"batch_no\\\":20250703,\\\"authorize_id\\\":\\\"225634\\\",\\\"card_type\\\":\\\"VISA\\\",\\\"card_num\\\":\\\"411111xxxxxx1111\\\",\\\"secure_hash\\\":null,\\\"avs_result_code\\\":null,\\\"avs_acq_response_code\\\":\\\"00\\\",\\\"captured_amount\\\":200,\\\"authorised_amount\\\":200,\\\"refunded_amount\\\":0,\\\"acs_eci\\\":\\\"05\\\"},\\\"is_hidden\\\":false,\\\"payment_key_claims\\\":{\\\"exp\\\":1751505891,\\\"extra\\\":[],\\\"pmk_ip\\\":\\\"46.202.156.191\\\",\\\"user_id\\\":9665,\\\"currency\\\":\\\"SAR\\\",\\\"order_id\\\":396551,\\\"amount_cents\\\":20000,\\\"billing_data\\\":{\\\"city\\\":\\\"NA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"floor\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"street\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"building\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"extra_description\\\":\\\"NA\\\"},\\\"integration_id\\\":11784,\\\"lock_order_when_paid\\\":true,\\\"single_payment_attempt\\\":false},\\\"error_occured\\\":false,\\\"is_live\\\":false,\\\"other_endpoint_reference\\\":null,\\\"refunded_amount_cents\\\":0,\\\"source_id\\\":-1,\\\"is_captured\\\":false,\\\"captured_amount\\\":0,\\\"merchant_staff_tag\\\":null,\\\"updated_at\\\":\\\"2025-07-03T03:25:44.508232+03:00\\\",\\\"is_settled\\\":false,\\\"bill_balanced\\\":false,\\\"is_bill\\\":false,\\\"owner\\\":9665,\\\"parent_transaction\\\":null}\"', '2025-07-03 00:25:45', '2025-07-03 00:25:45'),
(2, 7, '252631', 'card', 200.00, 'success', '\"{\\\"id\\\":252631,\\\"pending\\\":false,\\\"amount_cents\\\":20000,\\\"success\\\":true,\\\"is_auth\\\":false,\\\"is_capture\\\":false,\\\"is_standalone_payment\\\":true,\\\"is_voided\\\":false,\\\"is_refunded\\\":false,\\\"is_3d_secure\\\":true,\\\"integration_id\\\":11784,\\\"profile_id\\\":8506,\\\"has_parent_transaction\\\":false,\\\"order\\\":{\\\"id\\\":396551,\\\"created_at\\\":\\\"2025-07-03T03:24:51.110457+03:00\\\",\\\"delivery_needed\\\":false,\\\"merchant\\\":{\\\"id\\\":8506,\\\"created_at\\\":\\\"2025-04-18T16:33:32.223343+03:00\\\",\\\"phones\\\":[\\\"+966546608060\\\"],\\\"company_emails\\\":null,\\\"company_name\\\":\\\"Leen\\\",\\\"state\\\":null,\\\"country\\\":\\\"SAU\\\",\\\"city\\\":\\\"temp\\\",\\\"postal_code\\\":null,\\\"street\\\":null},\\\"collector\\\":null,\\\"amount_cents\\\":20000,\\\"shipping_data\\\":{\\\"id\\\":224208,\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"street\\\":\\\"NA\\\",\\\"building\\\":\\\"NA\\\",\\\"floor\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"city\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"extra_description\\\":null,\\\"shipping_method\\\":\\\"UNK\\\",\\\"order_id\\\":396551,\\\"order\\\":396551},\\\"currency\\\":\\\"SAR\\\",\\\"is_payment_locked\\\":false,\\\"is_return\\\":false,\\\"is_cancel\\\":false,\\\"is_returned\\\":false,\\\"is_canceled\\\":false,\\\"merchant_order_id\\\":\\\"7_1751502290\\\",\\\"wallet_notification\\\":null,\\\"paid_amount_cents\\\":20000,\\\"notify_user_with_email\\\":false,\\\"items\\\":[{\\\"name\\\":\\\"Deposit Payment #7\\\",\\\"description\\\":\\\"Deposit payment for service booking\\\",\\\"amount_cents\\\":20000,\\\"quantity\\\":1}],\\\"order_url\\\":\\\"https:\\\\\\/\\\\\\/ksa.paymob.com\\\\\\/standalone\\\\\\/?ref=i_LRR2ZkZOZ2k0OFNocXM0VTcxeGp0Y1ZhQT09X2pmREtJYnBnZEl6V041cjBDTjdlRlE9PQ\\\",\\\"commission_fees\\\":0,\\\"delivery_fees_cents\\\":0,\\\"delivery_vat_cents\\\":0,\\\"payment_method\\\":\\\"tbc\\\",\\\"merchant_staff_tag\\\":null,\\\"api_source\\\":\\\"OTHER\\\",\\\"data\\\":[],\\\"payment_status\\\":\\\"PAID\\\"},\\\"created_at\\\":\\\"2025-07-03T03:25:27.932837+03:00\\\",\\\"transaction_processed_callback_responses\\\":[{\\\"response\\\":\\\"txn_callback: exception [TXNCALLBACK] [TXN:252631] RESPONSE_NOT_OK\\\",\\\"callback_url\\\":\\\"https:\\\\\\/\\\\\\/leen.gulfcodes.com\\\\\\/api\\\\\\/payment\\\\\\/callback\\\",\\\"response_received_at\\\":\\\"2025-07-03T03:25:45.289790+03:00\\\"}],\\\"currency\\\":\\\"SAR\\\",\\\"source_data\\\":{\\\"pan\\\":\\\"1111\\\",\\\"type\\\":\\\"card\\\",\\\"tenure\\\":null,\\\"sub_type\\\":\\\"Visa\\\"},\\\"api_source\\\":\\\"IFRAME\\\",\\\"terminal_id\\\":null,\\\"merchant_commission\\\":0,\\\"installment\\\":null,\\\"discount_details\\\":[],\\\"is_void\\\":false,\\\"is_refund\\\":false,\\\"data\\\":{\\\"klass\\\":\\\"MigsPayment\\\",\\\"amount\\\":20000,\\\"acs_eci\\\":\\\"05\\\",\\\"message\\\":\\\"Approved\\\",\\\"batch_no\\\":20250703,\\\"card_num\\\":\\\"411111xxxxxx1111\\\",\\\"currency\\\":\\\"SAR\\\",\\\"merchant\\\":\\\"TEST601108800\\\",\\\"card_type\\\":\\\"VISA\\\",\\\"created_at\\\":\\\"2025-07-03T00:25:44.501586\\\",\\\"migs_order\\\":{\\\"id\\\":\\\"aa396551\\\",\\\"amount\\\":200,\\\"status\\\":\\\"CAPTURED\\\",\\\"currency\\\":\\\"SAR\\\",\\\"chargeback\\\":{\\\"amount\\\":0,\\\"currency\\\":\\\"SAR\\\"},\\\"creationTime\\\":\\\"2025-07-03T00:25:40.272Z\\\",\\\"merchantAmount\\\":200,\\\"lastUpdatedTime\\\":\\\"2025-07-03T00:25:44.440Z\\\",\\\"merchantCurrency\\\":\\\"SAR\\\",\\\"acceptPartialAmount\\\":false,\\\"totalCapturedAmount\\\":200,\\\"totalRefundedAmount\\\":0,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"merchantCategoryCode\\\":\\\"7372\\\",\\\"totalAuthorizedAmount\\\":200},\\\"order_info\\\":\\\"aa396551\\\",\\\"receipt_no\\\":\\\"518400225634\\\",\\\"migs_result\\\":\\\"SUCCESS\\\",\\\"secure_hash\\\":null,\\\"authorize_id\\\":\\\"225634\\\",\\\"transaction_no\\\":\\\"123456789012345\\\",\\\"avs_result_code\\\":null,\\\"captured_amount\\\":200,\\\"refunded_amount\\\":0,\\\"merchant_txn_ref\\\":\\\"252631\\\",\\\"migs_transaction\\\":{\\\"id\\\":\\\"252631\\\",\\\"stan\\\":\\\"225634\\\",\\\"type\\\":\\\"PAYMENT\\\",\\\"amount\\\":200,\\\"source\\\":\\\"INTERNET\\\",\\\"receipt\\\":\\\"518400225634\\\",\\\"acquirer\\\":{\\\"id\\\":\\\"NCB_S2I\\\",\\\"date\\\":\\\"0703\\\",\\\"batch\\\":20250703,\\\"timeZone\\\":\\\"+0300\\\",\\\"merchantId\\\":\\\"601108800\\\",\\\"transactionId\\\":\\\"123456789012345\\\",\\\"settlementDate\\\":\\\"2025-07-03\\\"},\\\"currency\\\":\\\"SAR\\\",\\\"terminal\\\":\\\"NCBS2I02\\\",\\\"authorizationCode\\\":\\\"225634\\\",\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\"},\\\"acq_response_code\\\":\\\"00\\\",\\\"authorised_amount\\\":200,\\\"txn_response_code\\\":\\\"APPROVED\\\",\\\"avs_acq_response_code\\\":\\\"00\\\",\\\"gateway_integration_pk\\\":11784},\\\"is_hidden\\\":false,\\\"payment_key_claims\\\":{\\\"exp\\\":1751505891,\\\"extra\\\":[],\\\"pmk_ip\\\":\\\"46.202.156.191\\\",\\\"user_id\\\":9665,\\\"currency\\\":\\\"SAR\\\",\\\"order_id\\\":396551,\\\"amount_cents\\\":20000,\\\"billing_data\\\":{\\\"city\\\":\\\"NA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"floor\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"street\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"building\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"extra_description\\\":\\\"NA\\\"},\\\"integration_id\\\":11784,\\\"lock_order_when_paid\\\":true,\\\"single_payment_attempt\\\":false},\\\"error_occured\\\":false,\\\"is_live\\\":false,\\\"other_endpoint_reference\\\":null,\\\"refunded_amount_cents\\\":0,\\\"source_id\\\":-1,\\\"is_captured\\\":false,\\\"captured_amount\\\":0,\\\"merchant_staff_tag\\\":null,\\\"updated_at\\\":\\\"2025-07-03T03:25:45.289916+03:00\\\",\\\"is_settled\\\":false,\\\"bill_balanced\\\":false,\\\"is_bill\\\":false,\\\"owner\\\":9665,\\\"parent_transaction\\\":null}\"', '2025-07-03 00:26:46', '2025-07-03 00:26:46'),
(3, 8, '252637', 'card', 225.00, 'success', '\"{\\\"id\\\":252637,\\\"pending\\\":false,\\\"amount_cents\\\":22500,\\\"success\\\":true,\\\"is_auth\\\":false,\\\"is_capture\\\":false,\\\"is_standalone_payment\\\":true,\\\"is_voided\\\":false,\\\"is_refunded\\\":false,\\\"is_3d_secure\\\":true,\\\"integration_id\\\":11784,\\\"profile_id\\\":8506,\\\"has_parent_transaction\\\":false,\\\"order\\\":{\\\"id\\\":396554,\\\"created_at\\\":\\\"2025-07-03T03:28:45.959704+03:00\\\",\\\"delivery_needed\\\":false,\\\"merchant\\\":{\\\"id\\\":8506,\\\"created_at\\\":\\\"2025-04-18T16:33:32.223343+03:00\\\",\\\"phones\\\":[\\\"+966546608060\\\"],\\\"company_emails\\\":null,\\\"company_name\\\":\\\"Leen\\\",\\\"state\\\":null,\\\"country\\\":\\\"SAU\\\",\\\"city\\\":\\\"temp\\\",\\\"postal_code\\\":null,\\\"street\\\":null},\\\"collector\\\":null,\\\"amount_cents\\\":22500,\\\"shipping_data\\\":{\\\"id\\\":224212,\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"street\\\":\\\"NA\\\",\\\"building\\\":\\\"NA\\\",\\\"floor\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"city\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"extra_description\\\":null,\\\"shipping_method\\\":\\\"UNK\\\",\\\"order_id\\\":396554,\\\"order\\\":396554},\\\"currency\\\":\\\"SAR\\\",\\\"is_payment_locked\\\":false,\\\"is_return\\\":false,\\\"is_cancel\\\":false,\\\"is_returned\\\":false,\\\"is_canceled\\\":false,\\\"merchant_order_id\\\":\\\"8_1751502525\\\",\\\"wallet_notification\\\":null,\\\"paid_amount_cents\\\":22500,\\\"notify_user_with_email\\\":false,\\\"items\\\":[{\\\"name\\\":\\\"Deposit Payment #8\\\",\\\"description\\\":\\\"Deposit payment for service booking\\\",\\\"amount_cents\\\":22500,\\\"quantity\\\":1}],\\\"order_url\\\":\\\"https:\\\\\\/\\\\\\/ksa.paymob.com\\\\\\/standalone\\\\\\/?ref=i_LRR2R3lvcnpDQURobEZtV3dDT3pPM0QvZz09X0pWbWVFYmNoU0JkN3lPZjR3ZWZxRGc9PQ\\\",\\\"commission_fees\\\":0,\\\"delivery_fees_cents\\\":0,\\\"delivery_vat_cents\\\":0,\\\"payment_method\\\":\\\"tbc\\\",\\\"merchant_staff_tag\\\":null,\\\"api_source\\\":\\\"OTHER\\\",\\\"data\\\":[],\\\"payment_status\\\":\\\"PAID\\\"},\\\"created_at\\\":\\\"2025-07-03T03:30:41.923399+03:00\\\",\\\"transaction_processed_callback_responses\\\":[],\\\"currency\\\":\\\"SAR\\\",\\\"source_data\\\":{\\\"pan\\\":\\\"1111\\\",\\\"type\\\":\\\"card\\\",\\\"tenure\\\":null,\\\"sub_type\\\":\\\"Visa\\\"},\\\"api_source\\\":\\\"IFRAME\\\",\\\"terminal_id\\\":null,\\\"merchant_commission\\\":0,\\\"installment\\\":null,\\\"discount_details\\\":[],\\\"is_void\\\":false,\\\"is_refund\\\":false,\\\"data\\\":{\\\"gateway_integration_pk\\\":11784,\\\"klass\\\":\\\"MigsPayment\\\",\\\"created_at\\\":\\\"2025-07-03T00:31:04.540280\\\",\\\"amount\\\":22500,\\\"currency\\\":\\\"SAR\\\",\\\"migs_order\\\":{\\\"acceptPartialAmount\\\":false,\\\"amount\\\":225,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"chargeback\\\":{\\\"amount\\\":0,\\\"currency\\\":\\\"SAR\\\"},\\\"creationTime\\\":\\\"2025-07-03T00:30:54.071Z\\\",\\\"currency\\\":\\\"SAR\\\",\\\"id\\\":\\\"aa396554\\\",\\\"lastUpdatedTime\\\":\\\"2025-07-03T00:31:04.471Z\\\",\\\"merchantAmount\\\":225,\\\"merchantCategoryCode\\\":\\\"7372\\\",\\\"merchantCurrency\\\":\\\"SAR\\\",\\\"status\\\":\\\"CAPTURED\\\",\\\"totalAuthorizedAmount\\\":225,\\\"totalCapturedAmount\\\":225,\\\"totalRefundedAmount\\\":0},\\\"merchant\\\":\\\"TEST601108800\\\",\\\"migs_result\\\":\\\"SUCCESS\\\",\\\"migs_transaction\\\":{\\\"acquirer\\\":{\\\"batch\\\":20250703,\\\"date\\\":\\\"0703\\\",\\\"id\\\":\\\"NCB_S2I\\\",\\\"merchantId\\\":\\\"601108800\\\",\\\"settlementDate\\\":\\\"2025-07-03\\\",\\\"timeZone\\\":\\\"+0300\\\",\\\"transactionId\\\":\\\"123456789012345\\\"},\\\"amount\\\":225,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"authorizationCode\\\":\\\"224781\\\",\\\"currency\\\":\\\"SAR\\\",\\\"id\\\":\\\"252637\\\",\\\"receipt\\\":\\\"518400224781\\\",\\\"source\\\":\\\"INTERNET\\\",\\\"stan\\\":\\\"224781\\\",\\\"terminal\\\":\\\"NCBS2I02\\\",\\\"type\\\":\\\"PAYMENT\\\"},\\\"txn_response_code\\\":\\\"APPROVED\\\",\\\"acq_response_code\\\":\\\"00\\\",\\\"message\\\":\\\"Approved\\\",\\\"merchant_txn_ref\\\":\\\"252637\\\",\\\"order_info\\\":\\\"aa396554\\\",\\\"receipt_no\\\":\\\"518400224781\\\",\\\"transaction_no\\\":\\\"123456789012345\\\",\\\"batch_no\\\":20250703,\\\"authorize_id\\\":\\\"224781\\\",\\\"card_type\\\":\\\"VISA\\\",\\\"card_num\\\":\\\"411111xxxxxx1111\\\",\\\"secure_hash\\\":null,\\\"avs_result_code\\\":null,\\\"avs_acq_response_code\\\":\\\"00\\\",\\\"captured_amount\\\":225,\\\"authorised_amount\\\":225,\\\"refunded_amount\\\":0,\\\"acs_eci\\\":\\\"05\\\"},\\\"is_hidden\\\":false,\\\"payment_key_claims\\\":{\\\"exp\\\":1751506126,\\\"extra\\\":[],\\\"pmk_ip\\\":\\\"46.202.156.191\\\",\\\"user_id\\\":9665,\\\"currency\\\":\\\"SAR\\\",\\\"order_id\\\":396554,\\\"amount_cents\\\":22500,\\\"billing_data\\\":{\\\"city\\\":\\\"NA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"floor\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"street\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"building\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"extra_description\\\":\\\"NA\\\"},\\\"integration_id\\\":11784,\\\"lock_order_when_paid\\\":true,\\\"single_payment_attempt\\\":false},\\\"error_occured\\\":false,\\\"is_live\\\":false,\\\"other_endpoint_reference\\\":null,\\\"refunded_amount_cents\\\":0,\\\"source_id\\\":-1,\\\"is_captured\\\":false,\\\"captured_amount\\\":0,\\\"merchant_staff_tag\\\":null,\\\"updated_at\\\":\\\"2025-07-03T03:31:04.547122+03:00\\\",\\\"is_settled\\\":false,\\\"bill_balanced\\\":false,\\\"is_bill\\\":false,\\\"owner\\\":9665,\\\"parent_transaction\\\":null}\"', '2025-07-03 00:31:04', '2025-07-03 00:31:04'),
(4, 8, '252637', 'card', 225.00, 'success', '\"{\\\"id\\\":252637,\\\"pending\\\":false,\\\"amount_cents\\\":22500,\\\"success\\\":true,\\\"is_auth\\\":false,\\\"is_capture\\\":false,\\\"is_standalone_payment\\\":true,\\\"is_voided\\\":false,\\\"is_refunded\\\":false,\\\"is_3d_secure\\\":true,\\\"integration_id\\\":11784,\\\"profile_id\\\":8506,\\\"has_parent_transaction\\\":false,\\\"order\\\":{\\\"id\\\":396554,\\\"created_at\\\":\\\"2025-07-03T03:28:45.959704+03:00\\\",\\\"delivery_needed\\\":false,\\\"merchant\\\":{\\\"id\\\":8506,\\\"created_at\\\":\\\"2025-04-18T16:33:32.223343+03:00\\\",\\\"phones\\\":[\\\"+966546608060\\\"],\\\"company_emails\\\":null,\\\"company_name\\\":\\\"Leen\\\",\\\"state\\\":null,\\\"country\\\":\\\"SAU\\\",\\\"city\\\":\\\"temp\\\",\\\"postal_code\\\":null,\\\"street\\\":null},\\\"collector\\\":null,\\\"amount_cents\\\":22500,\\\"shipping_data\\\":{\\\"id\\\":224212,\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"street\\\":\\\"NA\\\",\\\"building\\\":\\\"NA\\\",\\\"floor\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"city\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"extra_description\\\":null,\\\"shipping_method\\\":\\\"UNK\\\",\\\"order_id\\\":396554,\\\"order\\\":396554},\\\"currency\\\":\\\"SAR\\\",\\\"is_payment_locked\\\":false,\\\"is_return\\\":false,\\\"is_cancel\\\":false,\\\"is_returned\\\":false,\\\"is_canceled\\\":false,\\\"merchant_order_id\\\":\\\"8_1751502525\\\",\\\"wallet_notification\\\":null,\\\"paid_amount_cents\\\":22500,\\\"notify_user_with_email\\\":false,\\\"items\\\":[{\\\"name\\\":\\\"Deposit Payment #8\\\",\\\"description\\\":\\\"Deposit payment for service booking\\\",\\\"amount_cents\\\":22500,\\\"quantity\\\":1}],\\\"order_url\\\":\\\"https:\\\\\\/\\\\\\/ksa.paymob.com\\\\\\/standalone\\\\\\/?ref=i_LRR2R3lvcnpDQURobEZtV3dDT3pPM0QvZz09X0pWbWVFYmNoU0JkN3lPZjR3ZWZxRGc9PQ\\\",\\\"commission_fees\\\":0,\\\"delivery_fees_cents\\\":0,\\\"delivery_vat_cents\\\":0,\\\"payment_method\\\":\\\"tbc\\\",\\\"merchant_staff_tag\\\":null,\\\"api_source\\\":\\\"OTHER\\\",\\\"data\\\":[],\\\"payment_status\\\":\\\"PAID\\\"},\\\"created_at\\\":\\\"2025-07-03T03:30:41.923399+03:00\\\",\\\"transaction_processed_callback_responses\\\":[{\\\"response\\\":\\\"txn_callback: exception [TXNCALLBACK] [TXN:252637] RESPONSE_NOT_OK\\\",\\\"callback_url\\\":\\\"https:\\\\\\/\\\\\\/leen.gulfcodes.com\\\\\\/api\\\\\\/payment\\\\\\/callback\\\",\\\"response_received_at\\\":\\\"2025-07-03T03:31:05.200495+03:00\\\"}],\\\"currency\\\":\\\"SAR\\\",\\\"source_data\\\":{\\\"pan\\\":\\\"1111\\\",\\\"type\\\":\\\"card\\\",\\\"tenure\\\":null,\\\"sub_type\\\":\\\"Visa\\\"},\\\"api_source\\\":\\\"IFRAME\\\",\\\"terminal_id\\\":null,\\\"merchant_commission\\\":0,\\\"installment\\\":null,\\\"discount_details\\\":[],\\\"is_void\\\":false,\\\"is_refund\\\":false,\\\"data\\\":{\\\"klass\\\":\\\"MigsPayment\\\",\\\"amount\\\":22500,\\\"acs_eci\\\":\\\"05\\\",\\\"message\\\":\\\"Approved\\\",\\\"batch_no\\\":20250703,\\\"card_num\\\":\\\"411111xxxxxx1111\\\",\\\"currency\\\":\\\"SAR\\\",\\\"merchant\\\":\\\"TEST601108800\\\",\\\"card_type\\\":\\\"VISA\\\",\\\"created_at\\\":\\\"2025-07-03T00:31:04.540280\\\",\\\"migs_order\\\":{\\\"id\\\":\\\"aa396554\\\",\\\"amount\\\":225,\\\"status\\\":\\\"CAPTURED\\\",\\\"currency\\\":\\\"SAR\\\",\\\"chargeback\\\":{\\\"amount\\\":0,\\\"currency\\\":\\\"SAR\\\"},\\\"creationTime\\\":\\\"2025-07-03T00:30:54.071Z\\\",\\\"merchantAmount\\\":225,\\\"lastUpdatedTime\\\":\\\"2025-07-03T00:31:04.471Z\\\",\\\"merchantCurrency\\\":\\\"SAR\\\",\\\"acceptPartialAmount\\\":false,\\\"totalCapturedAmount\\\":225,\\\"totalRefundedAmount\\\":0,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"merchantCategoryCode\\\":\\\"7372\\\",\\\"totalAuthorizedAmount\\\":225},\\\"order_info\\\":\\\"aa396554\\\",\\\"receipt_no\\\":\\\"518400224781\\\",\\\"migs_result\\\":\\\"SUCCESS\\\",\\\"secure_hash\\\":null,\\\"authorize_id\\\":\\\"224781\\\",\\\"transaction_no\\\":\\\"123456789012345\\\",\\\"avs_result_code\\\":null,\\\"captured_amount\\\":225,\\\"refunded_amount\\\":0,\\\"merchant_txn_ref\\\":\\\"252637\\\",\\\"migs_transaction\\\":{\\\"id\\\":\\\"252637\\\",\\\"stan\\\":\\\"224781\\\",\\\"type\\\":\\\"PAYMENT\\\",\\\"amount\\\":225,\\\"source\\\":\\\"INTERNET\\\",\\\"receipt\\\":\\\"518400224781\\\",\\\"acquirer\\\":{\\\"id\\\":\\\"NCB_S2I\\\",\\\"date\\\":\\\"0703\\\",\\\"batch\\\":20250703,\\\"timeZone\\\":\\\"+0300\\\",\\\"merchantId\\\":\\\"601108800\\\",\\\"transactionId\\\":\\\"123456789012345\\\",\\\"settlementDate\\\":\\\"2025-07-03\\\"},\\\"currency\\\":\\\"SAR\\\",\\\"terminal\\\":\\\"NCBS2I02\\\",\\\"authorizationCode\\\":\\\"224781\\\",\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\"},\\\"acq_response_code\\\":\\\"00\\\",\\\"authorised_amount\\\":225,\\\"txn_response_code\\\":\\\"APPROVED\\\",\\\"avs_acq_response_code\\\":\\\"00\\\",\\\"gateway_integration_pk\\\":11784},\\\"is_hidden\\\":false,\\\"payment_key_claims\\\":{\\\"exp\\\":1751506126,\\\"extra\\\":[],\\\"pmk_ip\\\":\\\"46.202.156.191\\\",\\\"user_id\\\":9665,\\\"currency\\\":\\\"SAR\\\",\\\"order_id\\\":396554,\\\"amount_cents\\\":22500,\\\"billing_data\\\":{\\\"city\\\":\\\"NA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"floor\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"street\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"building\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"extra_description\\\":\\\"NA\\\"},\\\"integration_id\\\":11784,\\\"lock_order_when_paid\\\":true,\\\"single_payment_attempt\\\":false},\\\"error_occured\\\":false,\\\"is_live\\\":false,\\\"other_endpoint_reference\\\":null,\\\"refunded_amount_cents\\\":0,\\\"source_id\\\":-1,\\\"is_captured\\\":false,\\\"captured_amount\\\":0,\\\"merchant_staff_tag\\\":null,\\\"updated_at\\\":\\\"2025-07-03T03:31:05.200622+03:00\\\",\\\"is_settled\\\":false,\\\"bill_balanced\\\":false,\\\"is_bill\\\":false,\\\"owner\\\":9665,\\\"parent_transaction\\\":null}\"', '2025-07-03 00:32:05', '2025-07-03 00:32:05'),
(5, 9, '252646', 'card', 55.00, 'success', '\"{\\\"id\\\":252646,\\\"pending\\\":false,\\\"amount_cents\\\":5500,\\\"success\\\":true,\\\"is_auth\\\":false,\\\"is_capture\\\":false,\\\"is_standalone_payment\\\":true,\\\"is_voided\\\":false,\\\"is_refunded\\\":false,\\\"is_3d_secure\\\":true,\\\"integration_id\\\":11784,\\\"profile_id\\\":8506,\\\"has_parent_transaction\\\":false,\\\"order\\\":{\\\"id\\\":396565,\\\"created_at\\\":\\\"2025-07-03T03:37:40.448144+03:00\\\",\\\"delivery_needed\\\":false,\\\"merchant\\\":{\\\"id\\\":8506,\\\"created_at\\\":\\\"2025-04-18T16:33:32.223343+03:00\\\",\\\"phones\\\":[\\\"+966546608060\\\"],\\\"company_emails\\\":null,\\\"company_name\\\":\\\"Leen\\\",\\\"state\\\":null,\\\"country\\\":\\\"SAU\\\",\\\"city\\\":\\\"temp\\\",\\\"postal_code\\\":null,\\\"street\\\":null},\\\"collector\\\":null,\\\"amount_cents\\\":5500,\\\"shipping_data\\\":{\\\"id\\\":224220,\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"street\\\":\\\"NA\\\",\\\"building\\\":\\\"NA\\\",\\\"floor\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"city\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"extra_description\\\":null,\\\"shipping_method\\\":\\\"UNK\\\",\\\"order_id\\\":396565,\\\"order\\\":396565},\\\"currency\\\":\\\"SAR\\\",\\\"is_payment_locked\\\":false,\\\"is_return\\\":false,\\\"is_cancel\\\":false,\\\"is_returned\\\":false,\\\"is_canceled\\\":false,\\\"merchant_order_id\\\":\\\"9_1751503060\\\",\\\"wallet_notification\\\":null,\\\"paid_amount_cents\\\":5500,\\\"notify_user_with_email\\\":false,\\\"items\\\":[{\\\"name\\\":\\\"Service Payment #9\\\",\\\"description\\\":\\\"Full payment for service booking\\\",\\\"amount_cents\\\":5500,\\\"quantity\\\":1}],\\\"order_url\\\":\\\"https:\\\\\\/\\\\\\/ksa.paymob.com\\\\\\/standalone\\\\\\/?ref=i_LRR2aU1QbGdIc01JejdTV1FTVEtpYnpvUT09X1Zqc1ZmQUtpN1M1RUd4OWlvUFVCMkE9PQ\\\",\\\"commission_fees\\\":0,\\\"delivery_fees_cents\\\":0,\\\"delivery_vat_cents\\\":0,\\\"payment_method\\\":\\\"tbc\\\",\\\"merchant_staff_tag\\\":null,\\\"api_source\\\":\\\"OTHER\\\",\\\"data\\\":[],\\\"payment_status\\\":\\\"PAID\\\"},\\\"created_at\\\":\\\"2025-07-03T03:37:58.096262+03:00\\\",\\\"transaction_processed_callback_responses\\\":[],\\\"currency\\\":\\\"SAR\\\",\\\"source_data\\\":{\\\"pan\\\":\\\"1111\\\",\\\"type\\\":\\\"card\\\",\\\"tenure\\\":null,\\\"sub_type\\\":\\\"Visa\\\"},\\\"api_source\\\":\\\"IFRAME\\\",\\\"terminal_id\\\":null,\\\"merchant_commission\\\":0,\\\"installment\\\":null,\\\"discount_details\\\":[],\\\"is_void\\\":false,\\\"is_refund\\\":false,\\\"data\\\":{\\\"gateway_integration_pk\\\":11784,\\\"klass\\\":\\\"MigsPayment\\\",\\\"created_at\\\":\\\"2025-07-03T00:38:13.919221\\\",\\\"amount\\\":5500,\\\"currency\\\":\\\"SAR\\\",\\\"migs_order\\\":{\\\"acceptPartialAmount\\\":false,\\\"amount\\\":55,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"chargeback\\\":{\\\"amount\\\":0,\\\"currency\\\":\\\"SAR\\\"},\\\"creationTime\\\":\\\"2025-07-03T00:38:10.380Z\\\",\\\"currency\\\":\\\"SAR\\\",\\\"id\\\":\\\"aa396565\\\",\\\"lastUpdatedTime\\\":\\\"2025-07-03T00:38:13.855Z\\\",\\\"merchantAmount\\\":55,\\\"merchantCategoryCode\\\":\\\"7372\\\",\\\"merchantCurrency\\\":\\\"SAR\\\",\\\"status\\\":\\\"CAPTURED\\\",\\\"totalAuthorizedAmount\\\":55,\\\"totalCapturedAmount\\\":55,\\\"totalRefundedAmount\\\":0},\\\"merchant\\\":\\\"TEST601108800\\\",\\\"migs_result\\\":\\\"SUCCESS\\\",\\\"migs_transaction\\\":{\\\"acquirer\\\":{\\\"batch\\\":20250703,\\\"date\\\":\\\"0703\\\",\\\"id\\\":\\\"NCB_S2I\\\",\\\"merchantId\\\":\\\"601108800\\\",\\\"settlementDate\\\":\\\"2025-07-03\\\",\\\"timeZone\\\":\\\"+0300\\\",\\\"transactionId\\\":\\\"123456789012345\\\"},\\\"amount\\\":55,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"authorizationCode\\\":\\\"228778\\\",\\\"currency\\\":\\\"SAR\\\",\\\"id\\\":\\\"252646\\\",\\\"receipt\\\":\\\"518400228778\\\",\\\"source\\\":\\\"INTERNET\\\",\\\"stan\\\":\\\"228778\\\",\\\"terminal\\\":\\\"NCBS2I02\\\",\\\"type\\\":\\\"PAYMENT\\\"},\\\"txn_response_code\\\":\\\"APPROVED\\\",\\\"acq_response_code\\\":\\\"00\\\",\\\"message\\\":\\\"Approved\\\",\\\"merchant_txn_ref\\\":\\\"252646\\\",\\\"order_info\\\":\\\"aa396565\\\",\\\"receipt_no\\\":\\\"518400228778\\\",\\\"transaction_no\\\":\\\"123456789012345\\\",\\\"batch_no\\\":20250703,\\\"authorize_id\\\":\\\"228778\\\",\\\"card_type\\\":\\\"VISA\\\",\\\"card_num\\\":\\\"411111xxxxxx1111\\\",\\\"secure_hash\\\":null,\\\"avs_result_code\\\":null,\\\"avs_acq_response_code\\\":\\\"00\\\",\\\"captured_amount\\\":55,\\\"authorised_amount\\\":55,\\\"refunded_amount\\\":0,\\\"acs_eci\\\":\\\"05\\\"},\\\"is_hidden\\\":false,\\\"payment_key_claims\\\":{\\\"exp\\\":1751506660,\\\"extra\\\":[],\\\"pmk_ip\\\":\\\"46.202.156.191\\\",\\\"user_id\\\":9665,\\\"currency\\\":\\\"SAR\\\",\\\"order_id\\\":396565,\\\"amount_cents\\\":5500,\\\"billing_data\\\":{\\\"city\\\":\\\"NA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"floor\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"street\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"building\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"extra_description\\\":\\\"NA\\\"},\\\"integration_id\\\":11784,\\\"lock_order_when_paid\\\":true,\\\"single_payment_attempt\\\":false},\\\"error_occured\\\":false,\\\"is_live\\\":false,\\\"other_endpoint_reference\\\":null,\\\"refunded_amount_cents\\\":0,\\\"source_id\\\":-1,\\\"is_captured\\\":false,\\\"captured_amount\\\":0,\\\"merchant_staff_tag\\\":null,\\\"updated_at\\\":\\\"2025-07-03T03:38:13.925988+03:00\\\",\\\"is_settled\\\":false,\\\"bill_balanced\\\":false,\\\"is_bill\\\":false,\\\"owner\\\":9665,\\\"parent_transaction\\\":null}\"', '2025-07-03 00:38:14', '2025-07-03 00:38:14'),
(6, 9, '252646', 'card', 55.00, 'success', '\"{\\\"id\\\":252646,\\\"pending\\\":false,\\\"amount_cents\\\":5500,\\\"success\\\":true,\\\"is_auth\\\":false,\\\"is_capture\\\":false,\\\"is_standalone_payment\\\":true,\\\"is_voided\\\":false,\\\"is_refunded\\\":false,\\\"is_3d_secure\\\":true,\\\"integration_id\\\":11784,\\\"profile_id\\\":8506,\\\"has_parent_transaction\\\":false,\\\"order\\\":{\\\"id\\\":396565,\\\"created_at\\\":\\\"2025-07-03T03:37:40.448144+03:00\\\",\\\"delivery_needed\\\":false,\\\"merchant\\\":{\\\"id\\\":8506,\\\"created_at\\\":\\\"2025-04-18T16:33:32.223343+03:00\\\",\\\"phones\\\":[\\\"+966546608060\\\"],\\\"company_emails\\\":null,\\\"company_name\\\":\\\"Leen\\\",\\\"state\\\":null,\\\"country\\\":\\\"SAU\\\",\\\"city\\\":\\\"temp\\\",\\\"postal_code\\\":null,\\\"street\\\":null},\\\"collector\\\":null,\\\"amount_cents\\\":5500,\\\"shipping_data\\\":{\\\"id\\\":224220,\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"street\\\":\\\"NA\\\",\\\"building\\\":\\\"NA\\\",\\\"floor\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"city\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"extra_description\\\":null,\\\"shipping_method\\\":\\\"UNK\\\",\\\"order_id\\\":396565,\\\"order\\\":396565},\\\"currency\\\":\\\"SAR\\\",\\\"is_payment_locked\\\":false,\\\"is_return\\\":false,\\\"is_cancel\\\":false,\\\"is_returned\\\":false,\\\"is_canceled\\\":false,\\\"merchant_order_id\\\":\\\"9_1751503060\\\",\\\"wallet_notification\\\":null,\\\"paid_amount_cents\\\":5500,\\\"notify_user_with_email\\\":false,\\\"items\\\":[{\\\"name\\\":\\\"Service Payment #9\\\",\\\"description\\\":\\\"Full payment for service booking\\\",\\\"amount_cents\\\":5500,\\\"quantity\\\":1}],\\\"order_url\\\":\\\"https:\\\\\\/\\\\\\/ksa.paymob.com\\\\\\/standalone\\\\\\/?ref=i_LRR2aU1QbGdIc01JejdTV1FTVEtpYnpvUT09X1Zqc1ZmQUtpN1M1RUd4OWlvUFVCMkE9PQ\\\",\\\"commission_fees\\\":0,\\\"delivery_fees_cents\\\":0,\\\"delivery_vat_cents\\\":0,\\\"payment_method\\\":\\\"tbc\\\",\\\"merchant_staff_tag\\\":null,\\\"api_source\\\":\\\"OTHER\\\",\\\"data\\\":[],\\\"payment_status\\\":\\\"PAID\\\"},\\\"created_at\\\":\\\"2025-07-03T03:37:58.096262+03:00\\\",\\\"transaction_processed_callback_responses\\\":[{\\\"response\\\":\\\"txn_callback: exception [TXNCALLBACK] [TXN:252646] RESPONSE_NOT_OK\\\",\\\"callback_url\\\":\\\"https:\\\\\\/\\\\\\/leen.gulfcodes.com\\\\\\/api\\\\\\/payment\\\\\\/callback\\\",\\\"response_received_at\\\":\\\"2025-07-03T03:38:14.480188+03:00\\\"}],\\\"currency\\\":\\\"SAR\\\",\\\"source_data\\\":{\\\"pan\\\":\\\"1111\\\",\\\"type\\\":\\\"card\\\",\\\"tenure\\\":null,\\\"sub_type\\\":\\\"Visa\\\"},\\\"api_source\\\":\\\"IFRAME\\\",\\\"terminal_id\\\":null,\\\"merchant_commission\\\":0,\\\"installment\\\":null,\\\"discount_details\\\":[],\\\"is_void\\\":false,\\\"is_refund\\\":false,\\\"data\\\":{\\\"klass\\\":\\\"MigsPayment\\\",\\\"amount\\\":5500,\\\"acs_eci\\\":\\\"05\\\",\\\"message\\\":\\\"Approved\\\",\\\"batch_no\\\":20250703,\\\"card_num\\\":\\\"411111xxxxxx1111\\\",\\\"currency\\\":\\\"SAR\\\",\\\"merchant\\\":\\\"TEST601108800\\\",\\\"card_type\\\":\\\"VISA\\\",\\\"created_at\\\":\\\"2025-07-03T00:38:13.919221\\\",\\\"migs_order\\\":{\\\"id\\\":\\\"aa396565\\\",\\\"amount\\\":55,\\\"status\\\":\\\"CAPTURED\\\",\\\"currency\\\":\\\"SAR\\\",\\\"chargeback\\\":{\\\"amount\\\":0,\\\"currency\\\":\\\"SAR\\\"},\\\"creationTime\\\":\\\"2025-07-03T00:38:10.380Z\\\",\\\"merchantAmount\\\":55,\\\"lastUpdatedTime\\\":\\\"2025-07-03T00:38:13.855Z\\\",\\\"merchantCurrency\\\":\\\"SAR\\\",\\\"acceptPartialAmount\\\":false,\\\"totalCapturedAmount\\\":55,\\\"totalRefundedAmount\\\":0,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"merchantCategoryCode\\\":\\\"7372\\\",\\\"totalAuthorizedAmount\\\":55},\\\"order_info\\\":\\\"aa396565\\\",\\\"receipt_no\\\":\\\"518400228778\\\",\\\"migs_result\\\":\\\"SUCCESS\\\",\\\"secure_hash\\\":null,\\\"authorize_id\\\":\\\"228778\\\",\\\"transaction_no\\\":\\\"123456789012345\\\",\\\"avs_result_code\\\":null,\\\"captured_amount\\\":55,\\\"refunded_amount\\\":0,\\\"merchant_txn_ref\\\":\\\"252646\\\",\\\"migs_transaction\\\":{\\\"id\\\":\\\"252646\\\",\\\"stan\\\":\\\"228778\\\",\\\"type\\\":\\\"PAYMENT\\\",\\\"amount\\\":55,\\\"source\\\":\\\"INTERNET\\\",\\\"receipt\\\":\\\"518400228778\\\",\\\"acquirer\\\":{\\\"id\\\":\\\"NCB_S2I\\\",\\\"date\\\":\\\"0703\\\",\\\"batch\\\":20250703,\\\"timeZone\\\":\\\"+0300\\\",\\\"merchantId\\\":\\\"601108800\\\",\\\"transactionId\\\":\\\"123456789012345\\\",\\\"settlementDate\\\":\\\"2025-07-03\\\"},\\\"currency\\\":\\\"SAR\\\",\\\"terminal\\\":\\\"NCBS2I02\\\",\\\"authorizationCode\\\":\\\"228778\\\",\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\"},\\\"acq_response_code\\\":\\\"00\\\",\\\"authorised_amount\\\":55,\\\"txn_response_code\\\":\\\"APPROVED\\\",\\\"avs_acq_response_code\\\":\\\"00\\\",\\\"gateway_integration_pk\\\":11784},\\\"is_hidden\\\":false,\\\"payment_key_claims\\\":{\\\"exp\\\":1751506660,\\\"extra\\\":[],\\\"pmk_ip\\\":\\\"46.202.156.191\\\",\\\"user_id\\\":9665,\\\"currency\\\":\\\"SAR\\\",\\\"order_id\\\":396565,\\\"amount_cents\\\":5500,\\\"billing_data\\\":{\\\"city\\\":\\\"NA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"floor\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"street\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"building\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"extra_description\\\":\\\"NA\\\"},\\\"integration_id\\\":11784,\\\"lock_order_when_paid\\\":true,\\\"single_payment_attempt\\\":false},\\\"error_occured\\\":false,\\\"is_live\\\":false,\\\"other_endpoint_reference\\\":null,\\\"refunded_amount_cents\\\":0,\\\"source_id\\\":-1,\\\"is_captured\\\":false,\\\"captured_amount\\\":0,\\\"merchant_staff_tag\\\":null,\\\"updated_at\\\":\\\"2025-07-03T03:38:14.480329+03:00\\\",\\\"is_settled\\\":false,\\\"bill_balanced\\\":false,\\\"is_bill\\\":false,\\\"owner\\\":9665,\\\"parent_transaction\\\":null}\"', '2025-07-03 00:39:15', '2025-07-03 00:39:15'),
(7, 10, '252654', 'card', 250.00, 'success', '\"{\\\"id\\\":252654,\\\"pending\\\":false,\\\"amount_cents\\\":25000,\\\"success\\\":true,\\\"is_auth\\\":false,\\\"is_capture\\\":false,\\\"is_standalone_payment\\\":true,\\\"is_voided\\\":false,\\\"is_refunded\\\":false,\\\"is_3d_secure\\\":true,\\\"integration_id\\\":11784,\\\"profile_id\\\":8506,\\\"has_parent_transaction\\\":false,\\\"order\\\":{\\\"id\\\":396572,\\\"created_at\\\":\\\"2025-07-03T03:40:54.448534+03:00\\\",\\\"delivery_needed\\\":false,\\\"merchant\\\":{\\\"id\\\":8506,\\\"created_at\\\":\\\"2025-04-18T16:33:32.223343+03:00\\\",\\\"phones\\\":[\\\"+966546608060\\\"],\\\"company_emails\\\":null,\\\"company_name\\\":\\\"Leen\\\",\\\"state\\\":null,\\\"country\\\":\\\"SAU\\\",\\\"city\\\":\\\"temp\\\",\\\"postal_code\\\":null,\\\"street\\\":null},\\\"collector\\\":null,\\\"amount_cents\\\":25000,\\\"shipping_data\\\":{\\\"id\\\":224226,\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"street\\\":\\\"NA\\\",\\\"building\\\":\\\"NA\\\",\\\"floor\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"city\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"extra_description\\\":null,\\\"shipping_method\\\":\\\"UNK\\\",\\\"order_id\\\":396572,\\\"order\\\":396572},\\\"currency\\\":\\\"SAR\\\",\\\"is_payment_locked\\\":false,\\\"is_return\\\":false,\\\"is_cancel\\\":false,\\\"is_returned\\\":false,\\\"is_canceled\\\":false,\\\"merchant_order_id\\\":\\\"10_1751503254\\\",\\\"wallet_notification\\\":null,\\\"paid_amount_cents\\\":25000,\\\"notify_user_with_email\\\":false,\\\"items\\\":[{\\\"name\\\":\\\"Service Payment #10\\\",\\\"description\\\":\\\"Full payment for service booking\\\",\\\"amount_cents\\\":25000,\\\"quantity\\\":1}],\\\"order_url\\\":\\\"https:\\\\\\/\\\\\\/ksa.paymob.com\\\\\\/standalone\\\\\\/?ref=i_LRR2MW9KU0dhU1NnWGdFbDVkSzRQK2lIQT09X3NSTFBwUjJINVFBY2R4Mzd0OC9YeVE9PQ\\\",\\\"commission_fees\\\":0,\\\"delivery_fees_cents\\\":0,\\\"delivery_vat_cents\\\":0,\\\"payment_method\\\":\\\"tbc\\\",\\\"merchant_staff_tag\\\":null,\\\"api_source\\\":\\\"OTHER\\\",\\\"data\\\":[],\\\"payment_status\\\":\\\"PAID\\\"},\\\"created_at\\\":\\\"2025-07-03T03:41:09.316268+03:00\\\",\\\"transaction_processed_callback_responses\\\":[],\\\"currency\\\":\\\"SAR\\\",\\\"source_data\\\":{\\\"pan\\\":\\\"1111\\\",\\\"type\\\":\\\"card\\\",\\\"tenure\\\":null,\\\"sub_type\\\":\\\"Visa\\\"},\\\"api_source\\\":\\\"IFRAME\\\",\\\"terminal_id\\\":null,\\\"merchant_commission\\\":0,\\\"installment\\\":null,\\\"discount_details\\\":[],\\\"is_void\\\":false,\\\"is_refund\\\":false,\\\"data\\\":{\\\"gateway_integration_pk\\\":11784,\\\"klass\\\":\\\"MigsPayment\\\",\\\"created_at\\\":\\\"2025-07-03T00:41:25.797301\\\",\\\"amount\\\":25000,\\\"currency\\\":\\\"SAR\\\",\\\"migs_order\\\":{\\\"acceptPartialAmount\\\":false,\\\"amount\\\":250,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"chargeback\\\":{\\\"amount\\\":0,\\\"currency\\\":\\\"SAR\\\"},\\\"creationTime\\\":\\\"2025-07-03T00:41:21.139Z\\\",\\\"currency\\\":\\\"SAR\\\",\\\"id\\\":\\\"aa396572\\\",\\\"lastUpdatedTime\\\":\\\"2025-07-03T00:41:25.730Z\\\",\\\"merchantAmount\\\":250,\\\"merchantCategoryCode\\\":\\\"7372\\\",\\\"merchantCurrency\\\":\\\"SAR\\\",\\\"status\\\":\\\"CAPTURED\\\",\\\"totalAuthorizedAmount\\\":250,\\\"totalCapturedAmount\\\":250,\\\"totalRefundedAmount\\\":0},\\\"merchant\\\":\\\"TEST601108800\\\",\\\"migs_result\\\":\\\"SUCCESS\\\",\\\"migs_transaction\\\":{\\\"acquirer\\\":{\\\"batch\\\":20250703,\\\"date\\\":\\\"0703\\\",\\\"id\\\":\\\"NCB_S2I\\\",\\\"merchantId\\\":\\\"601108800\\\",\\\"settlementDate\\\":\\\"2025-07-03\\\",\\\"timeZone\\\":\\\"+0300\\\",\\\"transactionId\\\":\\\"123456789012345\\\"},\\\"amount\\\":250,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"authorizationCode\\\":\\\"237874\\\",\\\"currency\\\":\\\"SAR\\\",\\\"id\\\":\\\"252654\\\",\\\"receipt\\\":\\\"518400237874\\\",\\\"source\\\":\\\"INTERNET\\\",\\\"stan\\\":\\\"237874\\\",\\\"terminal\\\":\\\"NCBS2I02\\\",\\\"type\\\":\\\"PAYMENT\\\"},\\\"txn_response_code\\\":\\\"APPROVED\\\",\\\"acq_response_code\\\":\\\"00\\\",\\\"message\\\":\\\"Approved\\\",\\\"merchant_txn_ref\\\":\\\"252654\\\",\\\"order_info\\\":\\\"aa396572\\\",\\\"receipt_no\\\":\\\"518400237874\\\",\\\"transaction_no\\\":\\\"123456789012345\\\",\\\"batch_no\\\":20250703,\\\"authorize_id\\\":\\\"237874\\\",\\\"card_type\\\":\\\"VISA\\\",\\\"card_num\\\":\\\"411111xxxxxx1111\\\",\\\"secure_hash\\\":null,\\\"avs_result_code\\\":null,\\\"avs_acq_response_code\\\":\\\"00\\\",\\\"captured_amount\\\":250,\\\"authorised_amount\\\":250,\\\"refunded_amount\\\":0,\\\"acs_eci\\\":\\\"05\\\"},\\\"is_hidden\\\":false,\\\"payment_key_claims\\\":{\\\"exp\\\":1751506854,\\\"extra\\\":[],\\\"pmk_ip\\\":\\\"46.202.156.191\\\",\\\"user_id\\\":9665,\\\"currency\\\":\\\"SAR\\\",\\\"order_id\\\":396572,\\\"amount_cents\\\":25000,\\\"billing_data\\\":{\\\"city\\\":\\\"NA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"floor\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"street\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"building\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"extra_description\\\":\\\"NA\\\"},\\\"integration_id\\\":11784,\\\"lock_order_when_paid\\\":true,\\\"single_payment_attempt\\\":false},\\\"error_occured\\\":false,\\\"is_live\\\":false,\\\"other_endpoint_reference\\\":null,\\\"refunded_amount_cents\\\":0,\\\"source_id\\\":-1,\\\"is_captured\\\":false,\\\"captured_amount\\\":0,\\\"merchant_staff_tag\\\":null,\\\"updated_at\\\":\\\"2025-07-03T03:41:25.803803+03:00\\\",\\\"is_settled\\\":false,\\\"bill_balanced\\\":false,\\\"is_bill\\\":false,\\\"owner\\\":9665,\\\"parent_transaction\\\":null}\"', '2025-07-03 00:41:26', '2025-07-03 00:41:26');
INSERT INTO `payment_transactions` (`id`, `payment_id`, `transaction_id`, `type`, `amount`, `status`, `transaction_data`, `created_at`, `updated_at`) VALUES
(8, 10, '252654', 'card', 250.00, 'success', '\"{\\\"id\\\":252654,\\\"pending\\\":false,\\\"amount_cents\\\":25000,\\\"success\\\":true,\\\"is_auth\\\":false,\\\"is_capture\\\":false,\\\"is_standalone_payment\\\":true,\\\"is_voided\\\":false,\\\"is_refunded\\\":false,\\\"is_3d_secure\\\":true,\\\"integration_id\\\":11784,\\\"profile_id\\\":8506,\\\"has_parent_transaction\\\":false,\\\"order\\\":{\\\"id\\\":396572,\\\"created_at\\\":\\\"2025-07-03T03:40:54.448534+03:00\\\",\\\"delivery_needed\\\":false,\\\"merchant\\\":{\\\"id\\\":8506,\\\"created_at\\\":\\\"2025-04-18T16:33:32.223343+03:00\\\",\\\"phones\\\":[\\\"+966546608060\\\"],\\\"company_emails\\\":null,\\\"company_name\\\":\\\"Leen\\\",\\\"state\\\":null,\\\"country\\\":\\\"SAU\\\",\\\"city\\\":\\\"temp\\\",\\\"postal_code\\\":null,\\\"street\\\":null},\\\"collector\\\":null,\\\"amount_cents\\\":25000,\\\"shipping_data\\\":{\\\"id\\\":224226,\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"street\\\":\\\"NA\\\",\\\"building\\\":\\\"NA\\\",\\\"floor\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"city\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"extra_description\\\":null,\\\"shipping_method\\\":\\\"UNK\\\",\\\"order_id\\\":396572,\\\"order\\\":396572},\\\"currency\\\":\\\"SAR\\\",\\\"is_payment_locked\\\":false,\\\"is_return\\\":false,\\\"is_cancel\\\":false,\\\"is_returned\\\":false,\\\"is_canceled\\\":false,\\\"merchant_order_id\\\":\\\"10_1751503254\\\",\\\"wallet_notification\\\":null,\\\"paid_amount_cents\\\":25000,\\\"notify_user_with_email\\\":false,\\\"items\\\":[{\\\"name\\\":\\\"Service Payment #10\\\",\\\"description\\\":\\\"Full payment for service booking\\\",\\\"amount_cents\\\":25000,\\\"quantity\\\":1}],\\\"order_url\\\":\\\"https:\\\\\\/\\\\\\/ksa.paymob.com\\\\\\/standalone\\\\\\/?ref=i_LRR2MW9KU0dhU1NnWGdFbDVkSzRQK2lIQT09X3NSTFBwUjJINVFBY2R4Mzd0OC9YeVE9PQ\\\",\\\"commission_fees\\\":0,\\\"delivery_fees_cents\\\":0,\\\"delivery_vat_cents\\\":0,\\\"payment_method\\\":\\\"tbc\\\",\\\"merchant_staff_tag\\\":null,\\\"api_source\\\":\\\"OTHER\\\",\\\"data\\\":[],\\\"payment_status\\\":\\\"PAID\\\"},\\\"created_at\\\":\\\"2025-07-03T03:41:09.316268+03:00\\\",\\\"transaction_processed_callback_responses\\\":[{\\\"response\\\":\\\"txn_callback: exception [TXNCALLBACK] [TXN:252654] RESPONSE_NOT_OK\\\",\\\"callback_url\\\":\\\"https:\\\\\\/\\\\\\/leen.gulfcodes.com\\\\\\/api\\\\\\/payment\\\\\\/callback\\\",\\\"response_received_at\\\":\\\"2025-07-03T03:41:26.338005+03:00\\\"}],\\\"currency\\\":\\\"SAR\\\",\\\"source_data\\\":{\\\"pan\\\":\\\"1111\\\",\\\"type\\\":\\\"card\\\",\\\"tenure\\\":null,\\\"sub_type\\\":\\\"Visa\\\"},\\\"api_source\\\":\\\"IFRAME\\\",\\\"terminal_id\\\":null,\\\"merchant_commission\\\":0,\\\"installment\\\":null,\\\"discount_details\\\":[],\\\"is_void\\\":false,\\\"is_refund\\\":false,\\\"data\\\":{\\\"klass\\\":\\\"MigsPayment\\\",\\\"amount\\\":25000,\\\"acs_eci\\\":\\\"05\\\",\\\"message\\\":\\\"Approved\\\",\\\"batch_no\\\":20250703,\\\"card_num\\\":\\\"411111xxxxxx1111\\\",\\\"currency\\\":\\\"SAR\\\",\\\"merchant\\\":\\\"TEST601108800\\\",\\\"card_type\\\":\\\"VISA\\\",\\\"created_at\\\":\\\"2025-07-03T00:41:25.797301\\\",\\\"migs_order\\\":{\\\"id\\\":\\\"aa396572\\\",\\\"amount\\\":250,\\\"status\\\":\\\"CAPTURED\\\",\\\"currency\\\":\\\"SAR\\\",\\\"chargeback\\\":{\\\"amount\\\":0,\\\"currency\\\":\\\"SAR\\\"},\\\"creationTime\\\":\\\"2025-07-03T00:41:21.139Z\\\",\\\"merchantAmount\\\":250,\\\"lastUpdatedTime\\\":\\\"2025-07-03T00:41:25.730Z\\\",\\\"merchantCurrency\\\":\\\"SAR\\\",\\\"acceptPartialAmount\\\":false,\\\"totalCapturedAmount\\\":250,\\\"totalRefundedAmount\\\":0,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"merchantCategoryCode\\\":\\\"7372\\\",\\\"totalAuthorizedAmount\\\":250},\\\"order_info\\\":\\\"aa396572\\\",\\\"receipt_no\\\":\\\"518400237874\\\",\\\"migs_result\\\":\\\"SUCCESS\\\",\\\"secure_hash\\\":null,\\\"authorize_id\\\":\\\"237874\\\",\\\"transaction_no\\\":\\\"123456789012345\\\",\\\"avs_result_code\\\":null,\\\"captured_amount\\\":250,\\\"refunded_amount\\\":0,\\\"merchant_txn_ref\\\":\\\"252654\\\",\\\"migs_transaction\\\":{\\\"id\\\":\\\"252654\\\",\\\"stan\\\":\\\"237874\\\",\\\"type\\\":\\\"PAYMENT\\\",\\\"amount\\\":250,\\\"source\\\":\\\"INTERNET\\\",\\\"receipt\\\":\\\"518400237874\\\",\\\"acquirer\\\":{\\\"id\\\":\\\"NCB_S2I\\\",\\\"date\\\":\\\"0703\\\",\\\"batch\\\":20250703,\\\"timeZone\\\":\\\"+0300\\\",\\\"merchantId\\\":\\\"601108800\\\",\\\"transactionId\\\":\\\"123456789012345\\\",\\\"settlementDate\\\":\\\"2025-07-03\\\"},\\\"currency\\\":\\\"SAR\\\",\\\"terminal\\\":\\\"NCBS2I02\\\",\\\"authorizationCode\\\":\\\"237874\\\",\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\"},\\\"acq_response_code\\\":\\\"00\\\",\\\"authorised_amount\\\":250,\\\"txn_response_code\\\":\\\"APPROVED\\\",\\\"avs_acq_response_code\\\":\\\"00\\\",\\\"gateway_integration_pk\\\":11784},\\\"is_hidden\\\":false,\\\"payment_key_claims\\\":{\\\"exp\\\":1751506854,\\\"extra\\\":[],\\\"pmk_ip\\\":\\\"46.202.156.191\\\",\\\"user_id\\\":9665,\\\"currency\\\":\\\"SAR\\\",\\\"order_id\\\":396572,\\\"amount_cents\\\":25000,\\\"billing_data\\\":{\\\"city\\\":\\\"NA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"floor\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"street\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"building\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"extra_description\\\":\\\"NA\\\"},\\\"integration_id\\\":11784,\\\"lock_order_when_paid\\\":true,\\\"single_payment_attempt\\\":false},\\\"error_occured\\\":false,\\\"is_live\\\":false,\\\"other_endpoint_reference\\\":null,\\\"refunded_amount_cents\\\":0,\\\"source_id\\\":-1,\\\"is_captured\\\":false,\\\"captured_amount\\\":0,\\\"merchant_staff_tag\\\":null,\\\"updated_at\\\":\\\"2025-07-03T03:41:26.338142+03:00\\\",\\\"is_settled\\\":false,\\\"bill_balanced\\\":false,\\\"is_bill\\\":false,\\\"owner\\\":9665,\\\"parent_transaction\\\":null}\"', '2025-07-03 00:42:27', '2025-07-03 00:42:27'),
(9, 11, '252739', 'card', 238.00, 'success', '\"{\\\"id\\\":252739,\\\"pending\\\":false,\\\"amount_cents\\\":23800,\\\"success\\\":true,\\\"is_auth\\\":false,\\\"is_capture\\\":false,\\\"is_standalone_payment\\\":true,\\\"is_voided\\\":false,\\\"is_refunded\\\":false,\\\"is_3d_secure\\\":true,\\\"integration_id\\\":11784,\\\"profile_id\\\":8506,\\\"has_parent_transaction\\\":false,\\\"order\\\":{\\\"id\\\":396652,\\\"created_at\\\":\\\"2025-07-03T04:39:35.181500+03:00\\\",\\\"delivery_needed\\\":false,\\\"merchant\\\":{\\\"id\\\":8506,\\\"created_at\\\":\\\"2025-04-18T16:33:32.223343+03:00\\\",\\\"phones\\\":[\\\"+966546608060\\\"],\\\"company_emails\\\":null,\\\"company_name\\\":\\\"Leen\\\",\\\"state\\\":null,\\\"country\\\":\\\"SAU\\\",\\\"city\\\":\\\"temp\\\",\\\"postal_code\\\":null,\\\"street\\\":null},\\\"collector\\\":null,\\\"amount_cents\\\":23800,\\\"shipping_data\\\":{\\\"id\\\":224283,\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"street\\\":\\\"NA\\\",\\\"building\\\":\\\"NA\\\",\\\"floor\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"city\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"extra_description\\\":null,\\\"shipping_method\\\":\\\"UNK\\\",\\\"order_id\\\":396652,\\\"order\\\":396652},\\\"currency\\\":\\\"SAR\\\",\\\"is_payment_locked\\\":false,\\\"is_return\\\":false,\\\"is_cancel\\\":false,\\\"is_returned\\\":false,\\\"is_canceled\\\":false,\\\"merchant_order_id\\\":\\\"11_1751506774\\\",\\\"wallet_notification\\\":null,\\\"paid_amount_cents\\\":23800,\\\"notify_user_with_email\\\":false,\\\"items\\\":[{\\\"name\\\":\\\"Deposit Payment #11\\\",\\\"description\\\":\\\"Deposit payment for service booking\\\",\\\"amount_cents\\\":23800,\\\"quantity\\\":1}],\\\"order_url\\\":\\\"https:\\\\\\/\\\\\\/ksa.paymob.com\\\\\\/standalone\\\\\\/?ref=i_LRR2T2RiSGxHb3cveVQwWGJTYllpMGpLQT09X05jMHRDakM3YjFBZStlTVZGQTUrWVE9PQ\\\",\\\"commission_fees\\\":0,\\\"delivery_fees_cents\\\":0,\\\"delivery_vat_cents\\\":0,\\\"payment_method\\\":\\\"tbc\\\",\\\"merchant_staff_tag\\\":null,\\\"api_source\\\":\\\"OTHER\\\",\\\"data\\\":[],\\\"payment_status\\\":\\\"PAID\\\"},\\\"created_at\\\":\\\"2025-07-03T04:40:01.410268+03:00\\\",\\\"transaction_processed_callback_responses\\\":[],\\\"currency\\\":\\\"SAR\\\",\\\"source_data\\\":{\\\"pan\\\":\\\"1111\\\",\\\"type\\\":\\\"card\\\",\\\"tenure\\\":null,\\\"sub_type\\\":\\\"Visa\\\"},\\\"api_source\\\":\\\"IFRAME\\\",\\\"terminal_id\\\":null,\\\"merchant_commission\\\":0,\\\"installment\\\":null,\\\"discount_details\\\":[],\\\"is_void\\\":false,\\\"is_refund\\\":false,\\\"data\\\":{\\\"gateway_integration_pk\\\":11784,\\\"klass\\\":\\\"MigsPayment\\\",\\\"created_at\\\":\\\"2025-07-03T01:40:19.129636\\\",\\\"amount\\\":23800,\\\"currency\\\":\\\"SAR\\\",\\\"migs_order\\\":{\\\"acceptPartialAmount\\\":false,\\\"amount\\\":238,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"chargeback\\\":{\\\"amount\\\":0,\\\"currency\\\":\\\"SAR\\\"},\\\"creationTime\\\":\\\"2025-07-03T01:40:13.544Z\\\",\\\"currency\\\":\\\"SAR\\\",\\\"id\\\":\\\"aa396652\\\",\\\"lastUpdatedTime\\\":\\\"2025-07-03T01:40:19.060Z\\\",\\\"merchantAmount\\\":238,\\\"merchantCategoryCode\\\":\\\"7372\\\",\\\"merchantCurrency\\\":\\\"SAR\\\",\\\"status\\\":\\\"CAPTURED\\\",\\\"totalAuthorizedAmount\\\":238,\\\"totalCapturedAmount\\\":238,\\\"totalRefundedAmount\\\":0},\\\"merchant\\\":\\\"TEST601108800\\\",\\\"migs_result\\\":\\\"SUCCESS\\\",\\\"migs_transaction\\\":{\\\"acquirer\\\":{\\\"batch\\\":20250703,\\\"date\\\":\\\"0703\\\",\\\"id\\\":\\\"NCB_S2I\\\",\\\"merchantId\\\":\\\"601108800\\\",\\\"settlementDate\\\":\\\"2025-07-03\\\",\\\"timeZone\\\":\\\"+0300\\\",\\\"transactionId\\\":\\\"123456789012345\\\"},\\\"amount\\\":238,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"authorizationCode\\\":\\\"232329\\\",\\\"currency\\\":\\\"SAR\\\",\\\"id\\\":\\\"252739\\\",\\\"receipt\\\":\\\"518401232329\\\",\\\"source\\\":\\\"INTERNET\\\",\\\"stan\\\":\\\"232329\\\",\\\"terminal\\\":\\\"NCBS2I02\\\",\\\"type\\\":\\\"PAYMENT\\\"},\\\"txn_response_code\\\":\\\"APPROVED\\\",\\\"acq_response_code\\\":\\\"00\\\",\\\"message\\\":\\\"Approved\\\",\\\"merchant_txn_ref\\\":\\\"252739\\\",\\\"order_info\\\":\\\"aa396652\\\",\\\"receipt_no\\\":\\\"518401232329\\\",\\\"transaction_no\\\":\\\"123456789012345\\\",\\\"batch_no\\\":20250703,\\\"authorize_id\\\":\\\"232329\\\",\\\"card_type\\\":\\\"VISA\\\",\\\"card_num\\\":\\\"411111xxxxxx1111\\\",\\\"secure_hash\\\":null,\\\"avs_result_code\\\":null,\\\"avs_acq_response_code\\\":\\\"00\\\",\\\"captured_amount\\\":238,\\\"authorised_amount\\\":238,\\\"refunded_amount\\\":0,\\\"acs_eci\\\":\\\"05\\\"},\\\"is_hidden\\\":false,\\\"payment_key_claims\\\":{\\\"exp\\\":1751510375,\\\"extra\\\":[],\\\"pmk_ip\\\":\\\"46.202.156.191\\\",\\\"user_id\\\":9665,\\\"currency\\\":\\\"SAR\\\",\\\"order_id\\\":396652,\\\"amount_cents\\\":23800,\\\"billing_data\\\":{\\\"city\\\":\\\"NA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"floor\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"street\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"building\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"extra_description\\\":\\\"NA\\\"},\\\"integration_id\\\":11784,\\\"lock_order_when_paid\\\":true,\\\"single_payment_attempt\\\":false},\\\"error_occured\\\":false,\\\"is_live\\\":false,\\\"other_endpoint_reference\\\":null,\\\"refunded_amount_cents\\\":0,\\\"source_id\\\":-1,\\\"is_captured\\\":false,\\\"captured_amount\\\":0,\\\"merchant_staff_tag\\\":null,\\\"updated_at\\\":\\\"2025-07-03T04:40:19.136277+03:00\\\",\\\"is_settled\\\":false,\\\"bill_balanced\\\":false,\\\"is_bill\\\":false,\\\"owner\\\":9665,\\\"parent_transaction\\\":null}\"', '2025-07-03 01:40:19', '2025-07-03 01:40:19'),
(10, 11, '252739', 'card', 238.00, 'success', '\"{\\\"id\\\":252739,\\\"pending\\\":false,\\\"amount_cents\\\":23800,\\\"success\\\":true,\\\"is_auth\\\":false,\\\"is_capture\\\":false,\\\"is_standalone_payment\\\":true,\\\"is_voided\\\":false,\\\"is_refunded\\\":false,\\\"is_3d_secure\\\":true,\\\"integration_id\\\":11784,\\\"profile_id\\\":8506,\\\"has_parent_transaction\\\":false,\\\"order\\\":{\\\"id\\\":396652,\\\"created_at\\\":\\\"2025-07-03T04:39:35.181500+03:00\\\",\\\"delivery_needed\\\":false,\\\"merchant\\\":{\\\"id\\\":8506,\\\"created_at\\\":\\\"2025-04-18T16:33:32.223343+03:00\\\",\\\"phones\\\":[\\\"+966546608060\\\"],\\\"company_emails\\\":null,\\\"company_name\\\":\\\"Leen\\\",\\\"state\\\":null,\\\"country\\\":\\\"SAU\\\",\\\"city\\\":\\\"temp\\\",\\\"postal_code\\\":null,\\\"street\\\":null},\\\"collector\\\":null,\\\"amount_cents\\\":23800,\\\"shipping_data\\\":{\\\"id\\\":224283,\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"street\\\":\\\"NA\\\",\\\"building\\\":\\\"NA\\\",\\\"floor\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"city\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"extra_description\\\":null,\\\"shipping_method\\\":\\\"UNK\\\",\\\"order_id\\\":396652,\\\"order\\\":396652},\\\"currency\\\":\\\"SAR\\\",\\\"is_payment_locked\\\":false,\\\"is_return\\\":false,\\\"is_cancel\\\":false,\\\"is_returned\\\":false,\\\"is_canceled\\\":false,\\\"merchant_order_id\\\":\\\"11_1751506774\\\",\\\"wallet_notification\\\":null,\\\"paid_amount_cents\\\":23800,\\\"notify_user_with_email\\\":false,\\\"items\\\":[{\\\"name\\\":\\\"Deposit Payment #11\\\",\\\"description\\\":\\\"Deposit payment for service booking\\\",\\\"amount_cents\\\":23800,\\\"quantity\\\":1}],\\\"order_url\\\":\\\"https:\\\\\\/\\\\\\/ksa.paymob.com\\\\\\/standalone\\\\\\/?ref=i_LRR2T2RiSGxHb3cveVQwWGJTYllpMGpLQT09X05jMHRDakM3YjFBZStlTVZGQTUrWVE9PQ\\\",\\\"commission_fees\\\":0,\\\"delivery_fees_cents\\\":0,\\\"delivery_vat_cents\\\":0,\\\"payment_method\\\":\\\"tbc\\\",\\\"merchant_staff_tag\\\":null,\\\"api_source\\\":\\\"OTHER\\\",\\\"data\\\":[],\\\"payment_status\\\":\\\"PAID\\\"},\\\"created_at\\\":\\\"2025-07-03T04:40:01.410268+03:00\\\",\\\"transaction_processed_callback_responses\\\":[{\\\"response\\\":\\\"txn_callback: exception [TXNCALLBACK] [TXN:252739] RESPONSE_NOT_OK\\\",\\\"callback_url\\\":\\\"https:\\\\\\/\\\\\\/leen.gulfcodes.com\\\\\\/api\\\\\\/payment\\\\\\/callback\\\",\\\"response_received_at\\\":\\\"2025-07-03T04:40:19.855098+03:00\\\"}],\\\"currency\\\":\\\"SAR\\\",\\\"source_data\\\":{\\\"pan\\\":\\\"1111\\\",\\\"type\\\":\\\"card\\\",\\\"tenure\\\":null,\\\"sub_type\\\":\\\"Visa\\\"},\\\"api_source\\\":\\\"IFRAME\\\",\\\"terminal_id\\\":null,\\\"merchant_commission\\\":0,\\\"installment\\\":null,\\\"discount_details\\\":[],\\\"is_void\\\":false,\\\"is_refund\\\":false,\\\"data\\\":{\\\"klass\\\":\\\"MigsPayment\\\",\\\"amount\\\":23800,\\\"acs_eci\\\":\\\"05\\\",\\\"message\\\":\\\"Approved\\\",\\\"batch_no\\\":20250703,\\\"card_num\\\":\\\"411111xxxxxx1111\\\",\\\"currency\\\":\\\"SAR\\\",\\\"merchant\\\":\\\"TEST601108800\\\",\\\"card_type\\\":\\\"VISA\\\",\\\"created_at\\\":\\\"2025-07-03T01:40:19.129636\\\",\\\"migs_order\\\":{\\\"id\\\":\\\"aa396652\\\",\\\"amount\\\":238,\\\"status\\\":\\\"CAPTURED\\\",\\\"currency\\\":\\\"SAR\\\",\\\"chargeback\\\":{\\\"amount\\\":0,\\\"currency\\\":\\\"SAR\\\"},\\\"creationTime\\\":\\\"2025-07-03T01:40:13.544Z\\\",\\\"merchantAmount\\\":238,\\\"lastUpdatedTime\\\":\\\"2025-07-03T01:40:19.060Z\\\",\\\"merchantCurrency\\\":\\\"SAR\\\",\\\"acceptPartialAmount\\\":false,\\\"totalCapturedAmount\\\":238,\\\"totalRefundedAmount\\\":0,\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\",\\\"merchantCategoryCode\\\":\\\"7372\\\",\\\"totalAuthorizedAmount\\\":238},\\\"order_info\\\":\\\"aa396652\\\",\\\"receipt_no\\\":\\\"518401232329\\\",\\\"migs_result\\\":\\\"SUCCESS\\\",\\\"secure_hash\\\":null,\\\"authorize_id\\\":\\\"232329\\\",\\\"transaction_no\\\":\\\"123456789012345\\\",\\\"avs_result_code\\\":null,\\\"captured_amount\\\":238,\\\"refunded_amount\\\":0,\\\"merchant_txn_ref\\\":\\\"252739\\\",\\\"migs_transaction\\\":{\\\"id\\\":\\\"252739\\\",\\\"stan\\\":\\\"232329\\\",\\\"type\\\":\\\"PAYMENT\\\",\\\"amount\\\":238,\\\"source\\\":\\\"INTERNET\\\",\\\"receipt\\\":\\\"518401232329\\\",\\\"acquirer\\\":{\\\"id\\\":\\\"NCB_S2I\\\",\\\"date\\\":\\\"0703\\\",\\\"batch\\\":20250703,\\\"timeZone\\\":\\\"+0300\\\",\\\"merchantId\\\":\\\"601108800\\\",\\\"transactionId\\\":\\\"123456789012345\\\",\\\"settlementDate\\\":\\\"2025-07-03\\\"},\\\"currency\\\":\\\"SAR\\\",\\\"terminal\\\":\\\"NCBS2I02\\\",\\\"authorizationCode\\\":\\\"232329\\\",\\\"authenticationStatus\\\":\\\"AUTHENTICATION_SUCCESSFUL\\\"},\\\"acq_response_code\\\":\\\"00\\\",\\\"authorised_amount\\\":238,\\\"txn_response_code\\\":\\\"APPROVED\\\",\\\"avs_acq_response_code\\\":\\\"00\\\",\\\"gateway_integration_pk\\\":11784},\\\"is_hidden\\\":false,\\\"payment_key_claims\\\":{\\\"exp\\\":1751510375,\\\"extra\\\":[],\\\"pmk_ip\\\":\\\"46.202.156.191\\\",\\\"user_id\\\":9665,\\\"currency\\\":\\\"SAR\\\",\\\"order_id\\\":396652,\\\"amount_cents\\\":23800,\\\"billing_data\\\":{\\\"city\\\":\\\"NA\\\",\\\"email\\\":\\\"ss@mm.com\\\",\\\"floor\\\":\\\"NA\\\",\\\"state\\\":\\\"NA\\\",\\\"street\\\":\\\"NA\\\",\\\"country\\\":\\\"SA\\\",\\\"building\\\":\\\"NA\\\",\\\"apartment\\\":\\\"NA\\\",\\\"last_name\\\":\\\"\\\\u062e\\\\u0637\\\\u0627\\\\u0628\\\",\\\"first_name\\\":\\\"\\\\u0633\\\\u064a\\\\u062f\\\",\\\"postal_code\\\":\\\"NA\\\",\\\"phone_number\\\":\\\"9665483898980\\\",\\\"extra_description\\\":\\\"NA\\\"},\\\"integration_id\\\":11784,\\\"lock_order_when_paid\\\":true,\\\"single_payment_attempt\\\":false},\\\"error_occured\\\":false,\\\"is_live\\\":false,\\\"other_endpoint_reference\\\":null,\\\"refunded_amount_cents\\\":0,\\\"source_id\\\":-1,\\\"is_captured\\\":false,\\\"captured_amount\\\":0,\\\"merchant_staff_tag\\\":null,\\\"updated_at\\\":\\\"2025-07-03T04:40:19.855220+03:00\\\",\\\"is_settled\\\":false,\\\"bill_balanced\\\":false,\\\"is_bill\\\":false,\\\"owner\\\":9665,\\\"parent_transaction\\\":null}\"', '2025-07-03 01:41:20', '2025-07-03 01:41:20');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\Customer', 2, 'CustomerToken', '276c50c016957bf728c100051eed9d9461cf9ec179e2db5efe38450774f70b74', '[\"*\"]', NULL, NULL, '2025-06-26 20:34:52', '2025-06-26 20:34:52'),
(2, 'App\\Models\\Customer', 3, 'CustomerToken', '9026209c454eb07e410029fa3d2325e9227956d833a2d7aeac85759f59944b26', '[\"*\"]', NULL, NULL, '2025-06-26 20:41:13', '2025-06-26 20:41:13'),
(3, 'App\\Models\\Customer', 3, 'customerToken', '244d35922fd936364c4ae9c7bf92332104b84b425aae695c1d36b914c9b0746b', '[\"*\"]', NULL, NULL, '2025-06-27 09:28:58', '2025-06-27 09:28:58'),
(4, 'App\\Models\\Customer', 3, 'customerToken', 'fe9fb8cb3b58c2a415be9952e1d31715b826862200cce33ab3d53b87c70f3189', '[\"*\"]', NULL, NULL, '2025-06-27 09:58:14', '2025-06-27 09:58:14'),
(5, 'App\\Models\\Customer', 3, 'customerToken', '2fe0269ced1729015bcc5c6b02163a447b329e53fb169e7ea4609949d109e04b', '[\"*\"]', NULL, NULL, '2025-06-27 09:59:41', '2025-06-27 09:59:41'),
(6, 'App\\Models\\Customer', 3, 'customerToken', '2f68362526f6ce9dcb43a978daef48992b39044cf6a0d4d7a7cf4c2d44f31d36', '[\"*\"]', '2025-06-27 13:35:14', NULL, '2025-06-27 10:18:56', '2025-06-27 13:35:14'),
(7, 'App\\Models\\Customer', 3, 'customerToken', '80fe1306814fe2227b8da48d426685eedb3bd7d6c5fc159a7fe4265bc1d26e61', '[\"*\"]', '2025-06-27 13:46:12', NULL, '2025-06-27 13:35:35', '2025-06-27 13:46:12'),
(8, 'App\\Models\\Customer', 3, 'customerToken', '885bcc01b11c91374d60727e921028160b4935b965c22ebfdb1c1501fd03f657', '[\"*\"]', '2025-06-27 14:26:30', NULL, '2025-06-27 14:17:13', '2025-06-27 14:26:30'),
(11, 'App\\Models\\Customer', 4, 'CustomerToken', '23ddb10b90b3f1c30a62a0d30f223e92b8fb9c879dd4f16f03aad75d12383413', '[\"*\"]', '2025-06-27 21:28:22', NULL, '2025-06-27 21:28:00', '2025-06-27 21:28:22'),
(13, 'App\\Models\\Customer', 5, 'CustomerToken', 'd98093fbc221783b0596cdbf2a3bf0fc13b100d9fe67d3e8bff08fc223d1276f', '[\"*\"]', '2025-06-27 21:45:25', NULL, '2025-06-27 21:36:08', '2025-06-27 21:45:25'),
(14, 'App\\Models\\Customer', 6, 'CustomerToken', '8c7cd3264e76d27c50215d6c64f2f5f56749fb3d4a822cff28c591a9d06bab7d', '[\"*\"]', '2025-06-27 21:46:07', NULL, '2025-06-27 21:46:04', '2025-06-27 21:46:07'),
(16, 'App\\Models\\Customer', 7, 'CustomerToken', 'fa8180771618995df3995be31a4f74ab6391d9265f82439c53bbc0532ed3d63a', '[\"*\"]', '2025-06-28 06:47:11', NULL, '2025-06-27 21:52:03', '2025-06-28 06:47:11'),
(18, 'App\\Models\\Customer', 5, 'customerToken', '716476c776bc289700d30cf220b40fd03e150523735742c2d01d7a5be051b94f', '[\"*\"]', '2025-06-28 08:54:37', NULL, '2025-06-28 07:19:40', '2025-06-28 08:54:37'),
(19, 'App\\Models\\Customer', 5, 'customerToken', 'a29a20769612cb5e495183ea698a3878f817f0ba3f5c6baf873a9a6b99a216a2', '[\"*\"]', '2025-06-28 08:58:50', NULL, '2025-06-28 08:57:27', '2025-06-28 08:58:50'),
(20, 'App\\Models\\Customer', 5, 'customerToken', '5b2fa795e72f65022d04b467582d65d9a69425856ca95ecda6c37120e2e42424', '[\"*\"]', '2025-06-28 09:02:46', NULL, '2025-06-28 08:59:16', '2025-06-28 09:02:46'),
(21, 'App\\Models\\Customer', 5, 'customerToken', '9af643eef51d1578924c73a9af84934ee8219edd1f89d41ffd1a82a6574bb4d1', '[\"*\"]', '2025-06-28 09:06:53', NULL, '2025-06-28 09:03:50', '2025-06-28 09:06:53'),
(23, 'App\\Models\\Customer', 5, 'customerToken', '3a5e5f1e9b121a3cdf9fc6040e6d7a9008e083f436cdcc1f0dee84dfd32e7015', '[\"*\"]', '2025-06-28 09:45:58', NULL, '2025-06-28 09:41:05', '2025-06-28 09:45:58'),
(24, 'App\\Models\\Customer', 5, 'customerToken', 'cb22393268f44a21b1f6009fc59e5d2de65502aba64b6b04efe87a0163ba260f', '[\"*\"]', '2025-06-28 09:50:52', NULL, '2025-06-28 09:46:14', '2025-06-28 09:50:52'),
(26, 'App\\Models\\Seller', 2, 'SellerToken', '54db7db52b9d68b592994d9d9abd6bbb6676d4d8a86332377c449519975f1b94', '[\"seller\"]', NULL, NULL, '2025-06-28 12:25:55', '2025-06-28 12:25:55'),
(27, 'App\\Models\\Seller', 3, 'SellerToken', '698d30a5aa7d9754d7bb8ef1c7c2f6bd15b2648ab06cc136b9b7b16e4b822a99', '[\"seller\"]', NULL, NULL, '2025-06-28 12:34:18', '2025-06-28 12:34:18'),
(28, 'App\\Models\\Seller', 4, 'SellerToken', '9ae9341b5772be76d61e2fa6e2f7f2acfb7e74a6bd6ca53c5968d57510582f5b', '[\"seller\"]', NULL, NULL, '2025-06-28 15:12:06', '2025-06-28 15:12:06'),
(29, 'App\\Models\\Seller', 5, 'SellerToken', '1bdd698f788e1008988d3fd8681784c4cfa9a0b62d7960bd1dc192bdacb6b656', '[\"seller\"]', NULL, NULL, '2025-06-28 15:18:30', '2025-06-28 15:18:30'),
(30, 'App\\Models\\Seller', 6, 'SellerToken', '04b8c3edbd53a5ab5c69b9381f390676c4d55771f5f1a4d10deb37e4f71360c2', '[\"seller\"]', NULL, NULL, '2025-06-28 15:38:00', '2025-06-28 15:38:00'),
(31, 'App\\Models\\Seller', 7, 'SellerToken', 'ec12694ee747ede4330173800cd5a0117814b786fbc951c503c9b82ff4f0af89', '[\"seller\"]', NULL, NULL, '2025-06-28 16:27:01', '2025-06-28 16:27:01'),
(32, 'App\\Models\\Seller', 7, 'SellerToken', 'f1588cd086e7d638749f2cb552269f2d183b9c4c96e922f8afb1b789f0d41c76', '[\"seller\"]', NULL, NULL, '2025-06-28 16:37:04', '2025-06-28 16:37:04'),
(33, 'App\\Models\\Seller', 7, 'SellerToken', '5854de920276da12c668116e747a597dafc219ee768f18242103d67ed082e5d1', '[\"seller\"]', NULL, NULL, '2025-06-28 16:48:55', '2025-06-28 16:48:55'),
(34, 'App\\Models\\Seller', 7, 'SellerToken', 'cb607179932c8b8842d39a39cca5ac289d8a4ebacaef164b03e36da4abedf890', '[\"seller\"]', NULL, NULL, '2025-06-28 16:53:36', '2025-06-28 16:53:36'),
(35, 'App\\Models\\Seller', 7, 'SellerToken', '2c672d8d3a1663b7ad06a6decf844a8f6e06182ac1eafb14ccbe25b41e779eee', '[\"seller\"]', NULL, NULL, '2025-06-28 17:14:10', '2025-06-28 17:14:10'),
(36, 'App\\Models\\Seller', 7, 'SellerToken', 'cbb936d648d143715cdf01f74e7a822c9acf25b1709e56fff8f91805270c511f', '[\"seller\"]', NULL, NULL, '2025-06-28 17:24:32', '2025-06-28 17:24:32'),
(37, 'App\\Models\\Seller', 7, 'SellerToken', 'fd42bf2baca305a81ba1f366f7a375e4791fe444c3f7d01c4d0dffcbda63034d', '[\"seller\"]', NULL, NULL, '2025-06-28 17:44:17', '2025-06-28 17:44:17'),
(38, 'App\\Models\\Seller', 7, 'SellerToken', '633f28cd7e8f4d1f45fbb8c4d53b83c2e6d8daa061bf9b92d902b798666cfdbe', '[\"seller\"]', NULL, NULL, '2025-06-28 17:48:51', '2025-06-28 17:48:51'),
(39, 'App\\Models\\Seller', 7, 'SellerToken', 'f5b9a3dad395167f16a007610527154eb6fc37db43eb58368bbb5b6b9d93a148', '[\"seller\"]', '2025-06-28 18:16:50', NULL, '2025-06-28 17:59:34', '2025-06-28 18:16:50'),
(40, 'App\\Models\\Seller', 7, 'SellerToken', '7d7687857258fb1f69ed0a98c67a813b251ef697127caad24f01c569245d56e7', '[\"seller\"]', '2025-06-28 18:20:30', NULL, '2025-06-28 18:20:28', '2025-06-28 18:20:30'),
(41, 'App\\Models\\Seller', 7, 'SellerToken', 'b06cd1a0be1c56c678c0461af03dcce4dcd4d6ed21be988ae52eeac6a579f77e', '[\"seller\"]', '2025-06-28 18:25:52', NULL, '2025-06-28 18:25:40', '2025-06-28 18:25:52'),
(42, 'App\\Models\\Seller', 7, 'SellerToken', '93491b6c554b24f57f8a60af88b5f26a50745f4aca3b367535344ebd2f514ab3', '[\"seller\"]', '2025-06-28 18:30:38', NULL, '2025-06-28 18:30:35', '2025-06-28 18:30:38'),
(43, 'App\\Models\\Customer', 5, 'CustomerToken', '43fd6eecc6d354deca2a96d311b30e319280ffe82d20106b9bee891466398d83', '[\"customer\"]', NULL, NULL, '2025-06-28 18:34:21', '2025-06-28 18:34:21'),
(45, 'App\\Models\\Seller', 7, 'SellerToken', '6423ead292cdfd26d9a1f9e0339143988a9ed539dbbf3a62abca63353c53cb07', '[\"seller\"]', '2025-06-29 11:59:32', NULL, '2025-06-28 18:41:20', '2025-06-29 11:59:32'),
(46, 'App\\Models\\Seller', 7, 'SellerToken', '555bcf1bc70b415dcac811bda915bcd20f4736b01377dd50ebb3fa7cdd0be871', '[\"seller\"]', '2025-06-29 19:22:28', NULL, '2025-06-29 13:28:42', '2025-06-29 19:22:28'),
(47, 'App\\Models\\Seller', 7, 'SellerToken', '3352831a6fb9e819f68a100cb42f6bfb9fce6f37914971510264c07e75b56cbe', '[\"seller\"]', '2025-07-01 09:58:18', NULL, '2025-07-01 09:23:37', '2025-07-01 09:58:18'),
(48, 'App\\Models\\Seller', 7, 'SellerToken', '7980d73874b23474e6a0c76cf663d7381a84ab6cee975b7d89e88d3a51e2b9c2', '[\"seller\"]', '2025-07-01 11:17:37', NULL, '2025-07-01 10:32:33', '2025-07-01 11:17:37'),
(49, 'App\\Models\\Customer', 5, 'CustomerToken', 'c8f3dd74824caf01d070e94792148d90931f2a5a2d88e8e04dc98f03c5f07265', '[\"customer\"]', '2025-07-01 15:52:52', NULL, '2025-07-01 13:44:56', '2025-07-01 15:52:52'),
(50, 'App\\Models\\Customer', 5, 'CustomerToken', '12caffa1b909a3163559505a2a0afd50c825a354e10f0faa9c2f3358c67ea014', '[\"customer\"]', '2025-07-01 16:36:16', NULL, '2025-07-01 15:53:22', '2025-07-01 16:36:16'),
(51, 'App\\Models\\Customer', 5, 'CustomerToken', 'd53c92f4a8a33e3ea57a56796efded17b57037b70c0a47b552a68efd271d7296', '[\"customer\"]', '2025-07-01 18:29:19', NULL, '2025-07-01 16:36:30', '2025-07-01 18:29:19'),
(53, 'App\\Models\\Seller', 7, 'SellerToken', 'd6bbc36b27321d9c4a168b52eb2c79a1669baefc1bbf802ed05a48ebe62058b2', '[\"seller\"]', '2025-07-02 10:59:49', NULL, '2025-07-01 22:58:27', '2025-07-02 10:59:49'),
(54, 'App\\Models\\Customer', 5, 'CustomerToken', 'e560e7289a35b7abf9f09f8714cb0a9d9a655279ccfecdf0912f315dbf3d87af', '[\"customer\"]', '2025-07-02 14:52:31', NULL, '2025-07-02 11:00:57', '2025-07-02 14:52:31'),
(55, 'App\\Models\\Seller', 7, 'SellerToken', 'f069d72d6deb989cee250e9e54b302499ddd4348bf7b51a7376f5b857e35df70', '[\"seller\"]', '2025-07-02 20:34:02', NULL, '2025-07-02 20:33:18', '2025-07-02 20:34:02'),
(56, 'App\\Models\\Customer', 5, 'CustomerToken', 'a647f2e0da6d16efde74c772d44139c76c04204667bad8f5a1676d3aa1f3ac90', '[\"customer\"]', '2025-07-03 00:44:37', NULL, '2025-07-03 00:21:08', '2025-07-03 00:44:37'),
(57, 'App\\Models\\Seller', 7, 'SellerToken', 'a45b0d90ddcaebaad2b371a1155d904fa8c518e13a90acec9bfeeb039da9ec5d', '[\"seller\"]', '2025-07-03 01:37:32', NULL, '2025-07-03 01:34:13', '2025-07-03 01:37:32'),
(62, 'App\\Models\\Customer', 5, 'CustomerToken', 'e43053728fa4383175aff9936ff207b53f4903ddb579ed8b973ac5c972106bf9', '[\"customer\"]', '2025-07-08 19:10:55', NULL, '2025-07-08 19:03:06', '2025-07-08 19:10:55'),
(64, 'App\\Models\\Seller', 8, 'SellerToken', '0d17459bdf3894f7a136659637aec58a74bd325caea9c10409c4c7ff8244d480', '[\"seller\"]', NULL, NULL, '2025-07-08 19:09:02', '2025-07-08 19:09:02'),
(65, 'App\\Models\\Seller', 8, 'SellerToken', '4c7626f01319790ec8fe5738e7b047a4175827222a4f9d3fe9364e419b79225f', '[\"seller\"]', '2025-07-08 19:16:18', NULL, '2025-07-08 19:09:30', '2025-07-08 19:16:18'),
(66, 'App\\Models\\Customer', 5, 'CustomerToken', 'd0006c70ea4238a836199d276bf335044465d80c4ca9f218accdf5df3f82243b', '[\"customer\"]', '2025-07-08 19:16:34', NULL, '2025-07-08 19:16:34', '2025-07-08 19:16:34');

-- --------------------------------------------------------

--
-- Table structure for table `phone_verifications`
--

CREATE TABLE `phone_verifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(255) NOT NULL,
  `verification_code` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `attempts` int(11) NOT NULL DEFAULT 0,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL DEFAULT 'registration',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `phone_verifications`
--

INSERT INTO `phone_verifications` (`id`, `phone`, `verification_code`, `expires_at`, `attempts`, `verified`, `type`, `created_at`, `updated_at`) VALUES
(17, '966543444064', '4350', '2025-07-08 19:08:08', 1, 1, 'registration', '2025-07-08 19:08:03', '2025-07-08 19:08:08');

-- --------------------------------------------------------

--
-- Table structure for table `promotional_banners`
--

CREATE TABLE `promotional_banners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `action_text` varchar(255) DEFAULT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `is_limited_time` tinyint(1) NOT NULL DEFAULT 0,
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `target_audience` enum('all','customers','sellers') NOT NULL DEFAULT 'all',
  `link_type` enum('url','seller','home_service','studio_service') NOT NULL DEFAULT 'url',
  `linked_seller_id` bigint(20) UNSIGNED DEFAULT NULL,
  `linked_home_service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `linked_studio_service_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `promotional_banners`
--

INSERT INTO `promotional_banners` (`id`, `title`, `subtitle`, `image_path`, `action_text`, `action_url`, `is_limited_time`, `starts_at`, `expires_at`, `display_order`, `is_active`, `target_audience`, `link_type`, `linked_seller_id`, `linked_home_service_id`, `linked_studio_service_id`, `created_at`, `updated_at`) VALUES
(2, 'خصم خاص 20% لفترة محدودة', 'صالون الأصالة بالرياض', 'images/banners/1751294718.jpg', 'احجز الأن', NULL, 0, NULL, NULL, 0, 1, 'all', 'studio_service', NULL, NULL, 1, '2025-06-30 11:45:18', '2025-06-30 12:43:22'),
(3, 'خصم 30% على صبغ الشعر', 'صالون العيون الزرقاء', 'images/banners/1751296198.jpg', 'احجز الأن', NULL, 0, NULL, NULL, 1, 1, 'all', 'home_service', NULL, 2, NULL, '2025-06-30 12:09:58', '2025-06-30 12:09:58'),
(4, 'خصم 50% للأطفال حتى 12 سنة', 'مركز العناية بالبشرة', 'images/banners/1751296999.jpg', 'احجز الأن', NULL, 0, NULL, NULL, 2, 1, 'all', 'home_service', NULL, 1, NULL, '2025-06-30 12:23:19', '2025-06-30 13:09:28');

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `phone_verified_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'inactive',
  `request_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `seller_logo` varchar(255) DEFAULT NULL,
  `seller_banner` varchar(255) DEFAULT NULL,
  `license` varchar(255) DEFAULT NULL,
  `commercial_register` varchar(255) DEFAULT NULL,
  `commercial_register_document` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `request_rejection_reason` text DEFAULT NULL,
  `service_type` enum('home','studio','both') NOT NULL DEFAULT 'both',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `last_latitude` decimal(10,7) DEFAULT NULL,
  `last_longitude` decimal(10,7) DEFAULT NULL,
  `last_location_update` timestamp NULL DEFAULT NULL,
  `location_tracking_enabled` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`id`, `first_name`, `last_name`, `email`, `password`, `phone`, `phone_verified_at`, `status`, `request_status`, `seller_logo`, `seller_banner`, `license`, `commercial_register`, `commercial_register_document`, `location`, `request_rejection_reason`, `service_type`, `remember_token`, `created_at`, `updated_at`, `last_latitude`, `last_longitude`, `last_location_update`, `location_tracking_enabled`) VALUES
(1, 'بيوتي سنتر', '', 'seller@leen.com', '$2y$12$QjohNpMVIhEbXGIZ/O0Rqu0JZ9bZd3U29dy7IfNcMyT3nuKxi9ku.', '01094963620', NULL, 'active', 'approved', 'images/sellers/1751216450.jpg', NULL, NULL, NULL, NULL, 'طريق الملك عبد الله', NULL, 'both', NULL, '2025-06-22 15:49:48', '2025-06-29 14:00:50', NULL, NULL, NULL, 1),
(2, 'مساج سنتر', '', 'mm@ss.com', '$2y$12$R4HeV5sapOvZgecenkgj4OmHLVtgPVBvQknMdXEXtKpot0wJBpMtW', '966548676767', NULL, 'active', 'approved', 'images/sellers/1751323094.jpg', NULL, '50607080', NULL, NULL, 'الرياض', NULL, 'both', NULL, '2025-06-28 12:25:55', '2025-06-30 19:38:14', NULL, NULL, NULL, 1),
(3, 'صالون لافندر', '', 's@m.com', '$2y$12$g5Fz9Ysusnq8TP1Dej2iS.ry8z3ysiDKAZm8DWNzIQfWRr3YY7OLO', '966548308888', NULL, 'active', 'approved', 'images/sellers/1751323108.jpg', NULL, '55667788', '66543456', NULL, 'الدمام', NULL, 'studio', NULL, '2025-06-28 12:34:18', '2025-06-30 19:38:28', NULL, NULL, NULL, 1),
(4, 'صالون الرحاب', '', 'seso@koko.com', '$2y$12$o4SWlVG9/gzrDOsT8P0hbOIacEh7/vHoN66MmDObiye.y.7zl3q7.', '6778678687', NULL, 'active', 'approved', 'images/sellers/1751322904.jpg', NULL, '765445688', '76554', NULL, 'الرياض', NULL, 'home', NULL, '2025-06-28 15:12:06', '2025-06-30 19:35:04', NULL, NULL, NULL, 1),
(5, 'sss', 'mmm', 'sss@mmm.com', '$2y$12$qxknu5jtnaUwSSlbhbIYvO/vGI02qEr/7LILdUVHj3qQ/g7hGeFNC', '7897967657', NULL, 'inactive', 'pending', NULL, NULL, '77665544', '76543322', NULL, 'الرياض', NULL, 'both', NULL, '2025-06-28 15:18:30', '2025-06-28 15:18:30', NULL, NULL, NULL, 1),
(6, 'sssss', 'mmmmm', 'r@r.com', '$2y$12$wBm/Ivx/ScJnjlcrKmUQc.4dMDrcV9nhZSzvflmy1N0O1wVO2GHo.', '434545354', NULL, 'inactive', 'pending', 'images/sellers/1751300186.jpg', NULL, '7665444', 'u86544', NULL, 'الخرج', NULL, 'studio', NULL, '2025-06-28 15:38:00', '2025-06-30 13:16:26', NULL, NULL, NULL, 1),
(7, 'مركز تجميل الرياض', '', 'do@do.com', '$2y$12$q4IQ9PKwzK6MVJN3ix9a/.KsQPZnD47CCCvG/GQIx1.sU6WpvsXzi', '966548303000', '2025-06-28 19:36:51', 'active', 'approved', 'images/sellers/1751322623.jpg', 'images/sellers/1751322623.jpg', '66553322', '5645645', 'images/sellers/yrqj935qnBu1ju8LgfPpx7ZHgytMOrsfCzkGZ8F9.png', 'الرياض - حي القدس', NULL, 'both', NULL, '2025-06-28 16:27:01', '2025-06-30 19:35:30', NULL, NULL, NULL, 1),
(8, 'روان', 'روان', 'rawan.althobaiti@gmail.com', '$2y$12$pO35Xg6frKEWtx2WCZmkN.tt0R8ptzRoxnNcQ5ON7h1cGUGDN7SGS', '966543444064', NULL, 'active', 'pending', NULL, NULL, '10101010', NULL, NULL, 'الرياض', NULL, 'studio', NULL, '2025-07-08 19:09:02', '2025-07-08 19:09:30', NULL, NULL, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('BuaaUmSy1Mrz7fwrp2rum8A2GZ8JmFXAwwa8oT6C', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWXhUYTZFZkxpTFJyeDJWSkk0YWU1dmtQZE45SXR2UDBENTBsZkFvbSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NzA6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC8ud2VsbC1rbm93bi9hcHBzcGVjaWZpYy9jb20uY2hyb21lLmRldnRvb2xzLmpzb24iO319', 1752210099);

-- --------------------------------------------------------

--
-- Table structure for table `sms_notifications`
--

CREATE TABLE `sms_notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `phone` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `response_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`response_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sms_notifications`
--

INSERT INTO `sms_notifications` (`id`, `phone`, `message`, `type`, `status`, `response_data`, `created_at`, `updated_at`) VALUES
(1, '966548303000', 'كود التحقق الخاص بك في تطبيق Laravel هو: 976365', 'otp', 'failed', '{\"code\":400,\"message\":\"\\u0627\\u0633\\u0645 \\u0627\\u0644\\u0645\\u0631\\u0633\\u0644 \\u063a\\u064a\\u0631 \\u0645\\u0648\\u062c\\u0648\\u062f \\u0627\\u0648 \\u0644\\u0627 \\u062a\\u0645\\u0644\\u0643 \\u0635\\u0644\\u0627\\u062d\\u064a\\u0629\",\"not_senders\":[\"TechBack\"]}', '2025-06-26 19:00:28', '2025-06-26 19:00:28'),
(2, '966548303000', 'كود التحقق الخاص بك في تطبيق Laravel هو: 862533', 'otp', 'sent', '{\"job_id\":\"3dc9f224-52d9-11f0-b0e2-00163e08b4ee\",\"messages\":[{\"inserted_numbers\":1,\"message\":{\"account_id\":16448,\"approve_status\":3,\"job_id\":\"3dc9f224-52d9-11f0-b0e2-00163e08b4ee\",\"webhook\":null,\"token_uuid\":\"b266f632-5e0f-11ef-a2e2-00163ea420cf\",\"agent_browser\":\"\",\"agent_os\":\"\",\"agent_engine\":\"\",\"agent_device\":\"\",\"agent\":\"GuzzleHttp\\/7\",\"ip\":\"41.43.55.232\",\"from_site\":false,\"is_ads_groups\":0,\"app\":null,\"ver\":null,\"text\":\"\\u0643\\u0648\\u062f \\u0627\\u0644\\u062a\\u062d\\u0642\\u0642 \\u0627\\u0644\\u062e\\u0627\\u0635 \\u0628\\u0643 \\u0641\\u064a \\u062a\\u0637\\u0628\\u064a\\u0642 Laravel \\u0647\\u0648: 862533\",\"is_encrypted\":1,\"sender_id\":217302,\"sender_name\":\"TechPack\",\"encoding\":\"UTF16\",\"length\":47,\"per_message\":70,\"p_type\":1,\"remaining\":23,\"messages\":1,\"send_at\":null,\"send_at_zone\":null,\"send_at_system\":null,\"updated_at\":\"2025-06-26T22:02:24.000000Z\",\"created_at\":\"2025-06-26T22:02:24.000000Z\",\"id\":77804134,\"sms_message_numbers_count\":1},\"no_package\":[],\"has_more_iso_code\":[]}],\"code\":200,\"message\":\"\\u062a\\u0645\\u062a \\u0627\\u0644\\u0639\\u0645\\u0644\\u064a\\u0629 \\u0628\\u0646\\u062c\\u0627\\u062d\"}', '2025-06-26 19:02:24', '2025-06-26 19:02:24');

-- --------------------------------------------------------

--
-- Table structure for table `special_offers`
--

CREATE TABLE `special_offers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `discount_percentage` decimal(5,2) DEFAULT NULL,
  `discount_amount` decimal(10,2) DEFAULT NULL,
  `action_text` varchar(255) DEFAULT NULL,
  `action_url` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `starts_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `studio_services`
--

CREATE TABLE `studio_services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `sub_category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `gender` enum('male','female','both') NOT NULL DEFAULT 'both',
  `service_details` text DEFAULT NULL,
  `employees` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`employees`)),
  `price` decimal(10,2) NOT NULL,
  `booking_status` enum('available','unavailable') NOT NULL DEFAULT 'available',
  `discount` tinyint(1) NOT NULL DEFAULT 0,
  `percentage` decimal(5,2) NOT NULL DEFAULT 0.00,
  `points` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'مدة الخدمة بالدقائق',
  `description` text DEFAULT NULL COMMENT 'وصف مفصل للخدمة',
  `images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'صور الخدمة' CHECK (json_valid(`images`)),
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT 0.00 COMMENT 'نسبة الخصم',
  `discounted_price` decimal(10,2) DEFAULT NULL COMMENT 'السعر بعد الخصم',
  `location` varchar(255) DEFAULT NULL COMMENT 'موقع الاستوديو'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `studio_services`
--

INSERT INTO `studio_services` (`id`, `seller_id`, `category_id`, `sub_category_id`, `name`, `gender`, `service_details`, `employees`, `price`, `booking_status`, `discount`, `percentage`, `points`, `created_at`, `updated_at`, `duration`, `description`, `images`, `discount_percentage`, `discounted_price`, `location`) VALUES
(1, 7, 1, 2, 'خدمات الشعر بالاستوديو', 'female', NULL, NULL, 250.00, 'available', 0, 0.00, 0, '2025-06-29 19:18:02', '2025-07-01 09:55:52', 60, 'وصف لخدمة في اااستوديو يمكن للعملاء الحصول على خدمات ممتازة', '\"[\\\"1751374552_7nPcDf4cq0.jpg\\\",\\\"1751374552_SM2kVeKoTL.jpg\\\",\\\"1751374552_5v81V6ICPE.jpg\\\",\\\"1751374552_oL3aRbNiET.jpg\\\"]\"', 0.00, 250.00, 'الرياض - حي النخيل');

-- --------------------------------------------------------

--
-- Table structure for table `studio_service_bookings`
--

CREATE TABLE `studio_service_bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `studio_service_id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `seller_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `payment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date` date NOT NULL,
  `start_time` time NOT NULL,
  `payment_status` enum('pending','partially_paid','paid','failed','refunded') DEFAULT 'pending',
  `booking_status` enum('pending','confirmed','completed','cancelled','rejected') NOT NULL DEFAULT 'pending',
  `paid_amount` decimal(10,2) NOT NULL,
  `request_rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `studio_service_bookings`
--

INSERT INTO `studio_service_bookings` (`id`, `studio_service_id`, `customer_id`, `seller_id`, `employee_id`, `payment_id`, `date`, `start_time`, `payment_status`, `booking_status`, `paid_amount`, `request_rejection_reason`, `created_at`, `updated_at`) VALUES
(5, 1, 5, 7, 1, 4, '2025-07-03', '10:00:00', 'pending', 'pending', 250.00, NULL, '2025-07-02 14:38:01', '2025-07-02 14:38:05'),
(6, 1, 5, 7, 1, 5, '2025-07-03', '10:00:00', 'pending', 'pending', 250.00, NULL, '2025-07-02 14:50:48', '2025-07-02 14:50:54'),
(7, 1, 5, 7, 1, 6, '2025-07-04', '10:00:00', 'pending', 'pending', 250.00, NULL, '2025-07-03 00:21:34', '2025-07-03 00:21:45'),
(8, 1, 5, 7, 1, 7, '2025-07-04', '10:00:00', 'pending', 'pending', 250.00, NULL, '2025-07-03 00:24:46', '2025-07-03 00:24:50'),
(9, 1, 5, 7, 1, 8, '2025-07-05', '08:00:00', 'paid', 'pending', 250.00, NULL, '2025-07-03 00:28:30', '2025-07-03 00:28:45'),
(10, 1, 5, 7, 1, 10, '2025-07-04', '10:00:00', 'pending', 'pending', 250.00, NULL, '2025-07-03 00:40:51', '2025-07-03 00:40:53');

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sub_categories`
--

INSERT INTO `sub_categories` (`id`, `category_id`, `name`, `slug`, `description`, `is_active`, `display_order`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 'القص', 'alks', 'وصف تصنيف فرعي قص شعر', 1, 0, 'images/subcategories/1751212810.jpg', '2025-06-22 15:48:31', '2025-06-29 13:00:10'),
(2, 1, 'تسريحات', 'tsryhat', 'وصف تصنيف تسريحات الشعر', 1, 0, 'images/subcategories/1751212857.jpg', '2025-06-29 13:00:57', '2025-06-29 13:00:57'),
(3, 1, 'الفرد', 'alfrd', 'وصف تصنيف فرد الشعر', 1, 0, 'images/subcategories/1751212896.jpg', '2025-06-29 13:01:36', '2025-06-29 13:01:36'),
(4, 1, 'الصبغة', 'alsbgh', 'وصف تصنيف صبغ الشعر', 1, 0, 'images/subcategories/1751213071.jpg', '2025-06-29 13:04:31', '2025-06-29 13:04:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `categories_slug_unique` (`slug`);

--
-- Indexes for table `chat_rooms`
--
ALTER TABLE `chat_rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_rooms_seller_id_foreign` (`seller_id`),
  ADD KEY `chat_rooms_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_email_unique` (`email`),
  ADD UNIQUE KEY `customers_phone_unique` (`phone`);

--
-- Indexes for table `discount_applications`
--
ALTER TABLE `discount_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `discount_applications_payment_id_foreign` (`payment_id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employees_seller_id_foreign` (`seller_id`),
  ADD KEY `idx_employee_position` (`position`),
  ADD KEY `idx_employee_specialization` (`specialization`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `featured_professionals`
--
ALTER TABLE `featured_professionals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `featured_professionals_seller_id_foreign` (`seller_id`);

--
-- Indexes for table `featured_services`
--
ALTER TABLE `featured_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `featured_services_service_id_service_type_index` (`service_id`,`service_type`);

--
-- Indexes for table `home_services`
--
ALTER TABLE `home_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `home_services_seller_id_foreign` (`seller_id`),
  ADD KEY `home_services_category_id_foreign` (`category_id`),
  ADD KEY `home_services_sub_category_id_foreign` (`sub_category_id`);

--
-- Indexes for table `home_service_bookings`
--
ALTER TABLE `home_service_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `home_service_bookings_home_service_id_foreign` (`home_service_id`),
  ADD KEY `home_service_bookings_customer_id_foreign` (`customer_id`),
  ADD KEY `home_service_bookings_seller_id_foreign` (`seller_id`),
  ADD KEY `home_service_bookings_employee_id_foreign` (`employee_id`),
  ADD KEY `home_service_bookings_payment_id_foreign` (`payment_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_chat_room_id_foreign` (`chat_room_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_customer_id_foreign` (`customer_id`),
  ADD KEY `notifications_seller_id_foreign` (`seller_id`);

--
-- Indexes for table `order_references`
--
ALTER TABLE `order_references`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_references_payment_id_foreign` (`payment_id`),
  ADD KEY `order_references_reference_id_index` (`reference_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payments_user_id_status_index` (`user_id`,`status`),
  ADD KEY `payments_reference_id_index` (`reference_id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_transactions_payment_id_type_status_index` (`payment_id`,`type`,`status`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `phone_verifications`
--
ALTER TABLE `phone_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `phone_verifications_phone_index` (`phone`);

--
-- Indexes for table `promotional_banners`
--
ALTER TABLE `promotional_banners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotional_banners_linked_seller_id_foreign` (`linked_seller_id`),
  ADD KEY `promotional_banners_linked_home_service_id_foreign` (`linked_home_service_id`),
  ADD KEY `promotional_banners_linked_studio_service_id_foreign` (`linked_studio_service_id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sellers_email_unique` (`email`),
  ADD UNIQUE KEY `sellers_phone_unique` (`phone`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sms_notifications`
--
ALTER TABLE `sms_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sms_notifications_phone_type_index` (`phone`,`type`);

--
-- Indexes for table `special_offers`
--
ALTER TABLE `special_offers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `studio_services`
--
ALTER TABLE `studio_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `studio_services_seller_id_foreign` (`seller_id`),
  ADD KEY `studio_services_category_id_foreign` (`category_id`),
  ADD KEY `studio_services_sub_category_id_foreign` (`sub_category_id`);

--
-- Indexes for table `studio_service_bookings`
--
ALTER TABLE `studio_service_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `studio_service_bookings_studio_service_id_foreign` (`studio_service_id`),
  ADD KEY `studio_service_bookings_customer_id_foreign` (`customer_id`),
  ADD KEY `studio_service_bookings_seller_id_foreign` (`seller_id`),
  ADD KEY `studio_service_bookings_employee_id_foreign` (`employee_id`),
  ADD KEY `studio_service_bookings_payment_id_foreign` (`payment_id`);

--
-- Indexes for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sub_categories_slug_unique` (`slug`),
  ADD KEY `sub_categories_category_id_foreign` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `chat_rooms`
--
ALTER TABLE `chat_rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `discount_applications`
--
ALTER TABLE `discount_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `featured_professionals`
--
ALTER TABLE `featured_professionals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `featured_services`
--
ALTER TABLE `featured_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `home_services`
--
ALTER TABLE `home_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `home_service_bookings`
--
ALTER TABLE `home_service_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_references`
--
ALTER TABLE `order_references`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `phone_verifications`
--
ALTER TABLE `phone_verifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `promotional_banners`
--
ALTER TABLE `promotional_banners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `sms_notifications`
--
ALTER TABLE `sms_notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `special_offers`
--
ALTER TABLE `special_offers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `studio_services`
--
ALTER TABLE `studio_services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `studio_service_bookings`
--
ALTER TABLE `studio_service_bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_rooms`
--
ALTER TABLE `chat_rooms`
  ADD CONSTRAINT `chat_rooms_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chat_rooms_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `discount_applications`
--
ALTER TABLE `discount_applications`
  ADD CONSTRAINT `discount_applications_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `featured_professionals`
--
ALTER TABLE `featured_professionals`
  ADD CONSTRAINT `featured_professionals_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `home_services`
--
ALTER TABLE `home_services`
  ADD CONSTRAINT `home_services_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `home_services_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `home_services_sub_category_id_foreign` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `home_service_bookings`
--
ALTER TABLE `home_service_bookings`
  ADD CONSTRAINT `home_service_bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `home_service_bookings_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `home_service_bookings_home_service_id_foreign` FOREIGN KEY (`home_service_id`) REFERENCES `home_services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `home_service_bookings_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `home_service_bookings_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_chat_room_id_foreign` FOREIGN KEY (`chat_room_id`) REFERENCES `chat_rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_seller_id_foreign` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
